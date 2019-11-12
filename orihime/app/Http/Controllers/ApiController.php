<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\Product;
use App\Company;
use App\Calendar;


class ApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    /**
     * 注文新規登録API
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
            if($request->lacking_flg){
                $order->lacking_flg = $request->lacking_flg;

            }else{
                $order->lacking_flg = 0;
            }
            
            $order->remarks       = $request->remarks;
            $order->user_id       = Auth::id();

            $order->save();
            return '200';

        } catch(Exception $ex){
            return $ex;
        }
    }

    /**
     * 注文更新API
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
     * 注文削除API
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
     * 注文一覧取得API
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

        // 納品日の期間を検索条件に追加
        // date_default_timezone_set('Asia/Tokyo');
        $from = date_create($request->delivery_date);
        $to = date_create($request->delivery_date);
        $to->modify('+1 months - 1 days'); 
        $query->whereBetween('delivery_date', [$from, $to]);

        $orders = $query->get();


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

        $order = Order::select('*')
        ->addSelect('orders.id as order_id')
        ->join('products', 'orders.product_id', '=', 'products.id')
        ->join('companies', 'orders.company_id', '=', 'companies.id')
        ->where('orders.id', '=', $request->order_id)
        ->get();

        return $order;
    }

    /**
     * 会社一覧取得API
     */
    public function getCompanylist(){
        return Company::get();
    }

    /**
     * 製品一覧取得API
     * リクエストに会社IDが含まれている場合は絞って検索
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
     * 発送予定日取得API
     * TODO: 要エラーハンドリング
     */
    function getExpshipdate(Request $request)
    {
        $ddate = $request->delivery_date;
        $cid   = $request->company_id;

        // 輸送タイムラグを取得
        $transport_lag = Company::select('delivery_lag')->find($cid)->delivery_lag;

        // 営業日カレンダーを取得
        $calendarList = Calendar::
            where('holiday_flg', '=', '0')
            ->where('date', '<', $ddate)
            ->orderBy('date', 'desc')
            ->take($transport_lag)
            ->get();

        $resultDate = $calendarList[$transport_lag - 1]->date;

        return $resultDate;
    }

    /**
     * 注文管理表一覧取得API
     */
    public function mngSearch(Request $request)
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

        // 納品日の期間を検索条件に追加
        $from = date_create($request->delivery_date);
        $to = date_create($request->delivery_date);
        $to->modify('+1 months - 1 minutes'); 
        $query->whereBetween('delivery_date', [$from, $to]);
        
        // ソート
        $query->orderBy('product_code', 'asc')
            ->orderBy('delivery_code', 'asc')
            ->orderBy('delivery_date', 'asc');

        $orders = $query->get();
        //return $orders;

        // ②レコードを製品・納品先毎に集約する
        $results = array();
        $isDatePushed = 0;

        // 日付の配列
        $dateArr = array();
        for ($i = 1; $i <= 31; $i++) { array_push($dateArr, $i); }
        // デフォルトリスト作成用のカラの配列
        $blankArr = array();
        for ($i = 1; $i <= 31; $i++) {array_push($blankArr, '');}


        $currentProductId = '';
        $currentDeliveryId = '';
        $tmpList = array();

        // ③検索結果に対してループを回す
        foreach ($orders as $order) {

            $dlvDate = date_create($order->delivery_date)->format('d');


            // 品番・配送先が前回のループと同じ場合
            if($currentProductId == $order->product_id 
            && $currentDeliveryId == $order->delivery_id){

                // 結果セットの最後のKeyを取得
                $key = array_key_last($results);

                // 該当する配送日の位置に挿入
                $results[$key]['exp_ship_date'][$dlvDate - 1] = $order->exp_ship_date;
                $results[$key]['order_length'][$dlvDate - 1]  = $order->order_length;
                if ($order->result_length) {
                    $results[$key]['result_length'][$dlvDate - 1] = $order->result_length;
                } else {
                    $results[$key]['result_length'][$dlvDate - 1] = '-';
                }
                if ($order->roll_amount) {
                    $results[$key]['roll_amount'][$dlvDate - 1] = $order->roll_amount;
                } else {
                    $results[$key]['roll_amount'][$dlvDate - 1] = '-';
                }

            } else {
                // 品番・配送先が違う場合は上書きする
                $currentProductId = $order->product_id; 
                $currentDeliveryId = $order->delivery_id;

                // デフォルトのリストを作成
                $tmpList = array(
                    'product_code' => $order->product_code,
                    'delivery_name' => $order->delivery_name,
                    'product_id' => $order->product_id,
                    'delivery_code' => $order->delivery_code,
                    'delivery_date' => $dateArr,
                    'exp_ship_date' => $blankArr,
                    'order_length' => $blankArr,
                    'result_length' => $blankArr,
                    'roll_amount' => $blankArr,
                );

                // 該当する配送日の位置に挿入
                $tmpList['exp_ship_date'][$dlvDate - 1] = $order->exp_ship_date;
                $tmpList['order_length'][$dlvDate - 1] = $order->order_length;
                if($order->result_length){
                    $tmpList['result_length'][$dlvDate - 1] = $order->result_length;
                }else{
                    $tmpList['result_length'][$dlvDate - 1] = '-';
                }
                if($order->roll_amount){
                    $tmpList['roll_amount'][$dlvDate - 1] = $order->roll_amount;
                }else{
                    $tmpList['roll_amount'][$dlvDate - 1] = '-';
                }
                
                // 結果セットに挿入
                array_push($results, $tmpList);
            }
        }
        return $results;
    }
}
