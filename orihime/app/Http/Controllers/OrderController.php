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
        // 通常遷移時には一覧表を表示しない

        // 検索条件設定のためのデータ取得

        // ダミー
        $customer_list = [
            '' => '',
            '2A2C' => '帝人フロンティア',
            'SAI01' => 'セージ・オートモーティブ・インテリア'
        ];
        $delivery_list = [
            '' => '',
            '5493' => '東名化成（株）三重',
            'th001' => '田島縫製（鈴鹿事業所）'
        ];
        $material_list = [
            '' => '',
            'TR640A' => 'TR640A',
            'TR640AW' => 'TR640AW',
            'TR662A' => 'TR662A'
        ];
        $color_list = [
            '' => '',
            'Y605' => 'Y605',
            'B603' => 'B603'
        ];
        $date_list = [
            '' => '',
            '201908' => '2019年8月',
            '201909' => '2019年9月',
            '201910' => '2019年10月',
            '201911' => '2019年11月',
        ];
        $i = 1;
        $caldate = [];
        while ($i < 32) {
            array_push($caldate, $i);
            $i++;
        }


        // compactメソッドに変数名をStringで渡すと勝手に送ってくれる
        return view('order', compact('customer_list', 'delivery_list', 'material_list', 'color_list', 'date_list', 'caldate'));
        
    }

public function create_test(){
    $pd = Product::
    where('product_code', '=', 'TR640AW')
    ->first();
return $pd->id;
}

    /**
     * 新規作成API
     * TODO: あとで別のコントローラに変えるかも
     * TODO: product_id,company_idの特定が業務上できることを確認する
     * TODO: ベターなバリデーションの実装を確認する
     */
    public function create(Request $request)
    {
    
        $order = new Order;

        $order->product_id    = $request->product_id;
        $order->company_id    = $request->company_id;
        $order->opt_order_no  = $request->opt_order_no;
        $order->delivery_date = $request->delivery_date;
        $order->exp_ship_date = $request->exp_ship_date;

        // $order->ship_date = ''; 新規作成時には空欄
        $order->order_length = $request->order_length;
        // $order->result_length = ''; 新規作成時には空欄
        $order->lacking_flg = $request->lacking_flg;
        $order->remarks     = $request->remarks;
        $order->user_id     = Auth::id();

        $order->save();

        return 'finish';

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
     * 
     */
    function getCompany(?string $customer_code, ?string $delivery_code, ?string $enduser_code){
        $company = Company::
        where('customer_code', '=', $customer_code)
        ->where('delivery_code', '=', $delivery_code)
        ->where('enduser_code', '=', $enduser_code)
        ->first();
        return $company;
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
     * 
     */
    public function getProductlist(Request $request){
        return Product::
        select('*')
        ->join('agreements', 'agreements.product_id','=','products.id')
        ->where('company_id', '=', $request->company_id)
        ->get();
    }

    /**
     * オーダー検索API
     * TODO: あとで別のコントローラに変えるかも
     */
    public function search(Request $request)
    {
        // ①DBから取得
        $orders = DB::table('orders')
        ->select('*')
        ->addSelect('orders.id as order_id')
        ->join('products', 'orders.product_id','=','products.id')
        ->join('companies', 'orders.company_id','=','companies.id')
        ->get();

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


}
