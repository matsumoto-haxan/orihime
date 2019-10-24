<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Order;
use App\Product;
use App\Company;
use App\Calendar;




// ↓クエリビルダでSQLを発行するため
// ロジッククラスに切り出すかも
use DB;

/**
 * 注文入力画面のコントローラクラス
 */
class OrderController extends Controller
{
    /**
     * コンストラクタ
     * インスタンス作成時にauthミドルウェアを呼び出す
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * 通常遷移
     */
    public function index()
    {
        return view('order');   
    }



    /**
     * 新規登録API
     * TODO: あとで別のコントローラに変えるかも
     */
    public function create(Request $request)
    {

        $order = new Order;
        try{
            $order->product_id    = $request->product_id;
            $order->company_id    = $request->company_id;
            $order->opt_order_no  = $request->opt_order_no;
            $order->delivery_date = $request->delivery_date;
            $order->exp_ship_date = $request->exp_ship_date;
            $order->order_length  = $request->order_length;
            $order->roll_amount   = $request->roll_amount;
            $order->lacking_flg   = $request->lacking_flg;
            $order->remarks       = $request->remarks;
            $order->user_id       = Auth::id();

            $order->save();
            return '200';

        } catch(Exception $ex){
            return $ex;
        }
    }

    /**
     * 更新API
     * TODO: あとで別のコントローラに変えるかも
     */
    public function update(Request $request)
    {
        try{
            $updArray = [
                'delivery_date' => $request->delivery_date,
                'exp_ship_date' => $request->exp_ship_date,
                'ship_date' => $request->ship_date,
                'order_length' => $request->order_length,
                'result_length' => $request->result_length,
                'roll_amount' => $request->roll_amount,
                'lacking_flg' => $request->lacking_flg,
                'remarks' => $request->remarks,
                'user_id' => Auth::id()
            ];
            $order = Order::where('id', $request->order_id)->update($updArray);
            return '200';
        } catch(Exception $ex){
            return $ex;
        }
    }

    /**
     * 削除API
     * TODO: あとで別のコントローラに変えるかも
     */
    public function delete(Request $request)
    {
        try{
            $order = Order::destroy($request->order_id);
            return '200';
        } catch(Exception $ex){
            return $ex;
        }
    }

    /**
     *
     */
    function getCompany(?string $customer_code, ?string $delivery_code, ?string $enduser_code)
    {
        $company = Company::
            where('customer_code', '=', $customer_code)
            ->where('delivery_code', '=', $delivery_code)
            ->where('enduser_code', '=', $enduser_code)
            ->first();
        return $company;
    }


    /**
     * 
     */
    function getProduct(?string $product_code, ?string $material_code, ?string $color_code){
        $product = Product::
        where('product_code', '=', $product_code)
        ->where('material_code', '=', $material_code)
        ->where('color_code', '=', $color_code)
        ->first();
        $result = array(
            'id' => $product->id,
            'product_code' => $product->product_code,
            'material_code' => $product->material_code,
            'color_code' => $product->color_code,
            'roll_length' => $product->roll_length
        );
        return $result;
    }


    /**
     * 発送予定日取得API
     * ※ロジックは切り出すかも
     * // TODO: 要エラーハンドリング
     */
    function getExpshipdate(Request $request){
        $ddate = $request->delivery_date;
        $cid = $request->company_id;
        
        // 輸送タイムラグを取得
        $transport_lag = Company::select('delivery_lag')->find($cid)->delivery_lag;
        
        // 営業日カレンダーを取得
        $calendarList = Calendar::
        where('holiday_flg', '=', '0')
        ->where('date', '<', $ddate)
        ->orderBy('date', 'desc')
        ->take($transport_lag)
        ->get();

        $resultDate = $calendarList[$transport_lag -1]->date;

        return $resultDate;
    }


    /**
     * 
     */
    public function getCompanylist(){
        return Company::get();
    }

    /**
     * 製品一覧取得API
     */
    public function getProductlist(Request $request){

        if($request->company_id != ''){
            return Product::
            select('*')
            ->join('agreements', 'agreements.product_id', '=', 'products.id')
            ->where('company_id', '=', $request->company_id)
            ->get();
        }else{
            return Product::
            select('*')
            ->join('agreements', 'agreements.product_id', '=', 'products.id')
            ->get();
        }
        
        
    }

    /**
     * オーダー検索API
     * TODO: あとで別のコントローラに変えるかも
     */
    public function search(Request $request)
    {
        // ①DBから取得
        $query = Order::query();
        $query->select('*')
            ->addSelect('orders.id as order_id')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->join('companies', 'orders.company_id', '=', 'companies.id');

        // 会社情報の検索条件を追加
        if($request->company_id){
            $query->where('company_id', '=', $request->company_id);
        }else if($request->customer_code){
            $query->where('customer_code', '=', $request->customer_code);
        }

        // 製品情報の検索条件を追加
        if ($request->product_id) {
            $query->where('product_id', '=', $request->product_id);
        } else {
            if ($request->product_code) {
                $query->where('product_code', '=', $request->product_code);
                if($request->material_code){
                    $query->where('material_code', '=', $request->material_code);
                }
            }
        }
        $orders = $query->get();

        // ↓確認
        // return $orders;


        // ②レコードを製品・納品先毎に集約する
        $results = array();
        $isDatePushed = 0;

        // 検索結果に対してループを回す
        foreach ($orders as $order) {
            $o_prd = $order->product_id;
            $o_cmp = $order->company_id;
            $isDatePushed = 0;
            $tmpOrder = (object)array();

            // 返却データに対してループを回して確認
            foreach($results as $result){
                $r_prd = $result->product_id;
                $r_cmp = $result->company_id;

                // すでに返却データに登録されていた場合
                if($o_prd == $r_prd && $o_cmp == $r_cmp){

                    $dayStr = date('j', strtotime($order->delivery_date));

                    // 登録済みの1~31を回す
                    foreach($result->delivery_date as &$dd){
                        // 該当する日付の配列を検索結果の情報で上書き
                        if($dd['day'] == $dayStr){
                            $dd['order_id'] = $order->order_id;
                            $dd['order_length'] = $order->order_length;
                            $dd['lacking_flg'] = $order->lacking_flg;
                            break;
                        }
                    }
                    $isDatePushed += 1;
                    break;
                }
            }

            // もし返却データに登録されていなければ、1レコード追加
            if($isDatePushed == 0){
                $tmpOrder->product_id = $order->product_id;
                $tmpOrder->company_id = $order->company_id;
                $tmpOrder->product_code = $order->product_code;
                $tmpOrder->delivery_name = $order->delivery_name;
                
                $tmpOrder->delivery_date = array();                
                $dayStr = date('j', strtotime($order->delivery_date));

                // 1~31まで回す
                for ($i = 1; $i <= 31; $i++) {
                    // カラの日付配列を作成
                    $od = array(
                        'order_id'     => '',
                        'day'          => $i,
                        'order_length' => '',
                        'lacking_flg'  => 0,
                    );

                    // 該当する日付の配列を検索結果の情報で上書き
                    if($dayStr == $i){
                        
                        $od = array(
                            'order_id'     => $order->order_id,
                            'day'          => $dayStr,
                            'order_length' => $order->order_length,
                            'lacking_flg'  => $order->lacking_flg,
                        );
                    }
                    array_push($tmpOrder->delivery_date, $od);
                }

                array_push($results, $tmpOrder);
            }
        }

        return $results;
    }


    /**
     * 注文詳細取得API
     */
    public function getDetail(Request $request){

        $order = DB::table('orders')
        ->select('*')
        ->addSelect('orders.id as order_id')
        ->join('products', 'orders.product_id', '=', 'products.id')
        ->join('companies', 'orders.company_id', '=', 'companies.id')
        ->where('orders.id', '=', $request->order_id)
        ->get();

        return $order;
    }


}
