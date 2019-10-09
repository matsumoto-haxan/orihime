<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;

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




        // 適当に変数に値を入れる
        $message = 'hello';
        $testarray = ['yeah', 'welcome!'];

        // compactメソッドに変数名をStringで渡すと勝手に送ってくれる
        return view('order', compact('message', 'testarray', 'customer_list', 'delivery_list', 'material_list', 'color_list', 'date_list', 'caldate'));
        
    }

    /**
     * 新規作成API
     * TODO: あとで別のコントローラに変えるかも
     */
    public function create()
    {
        return "create";
    }

    /**
     * オーダー検索API
     * TODO: あとで別のコントローラに変えるかも
     */
    public function search(Request $request)
    {
        // $orders = Order::all();


        $orders = DB::table('orders')
        ->join('products', 'orders.product_id','=','products.id')
        ->join('companies', 'orders.company_id','=','companies.id')
        ->get();

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

                // すでに返却データに登録されていたら、ship_dateに追加してbreak
                if($o_prd == $r_prd && $o_cmp == $r_cmp){
                    if ($order->ship_date) {
                        $od = array(
                            'day'=>date('j', $order->ship_date),
                            'order_length'=>$order->order_length,
                            'lacking_flg'=>$order->lacking_flg,
                        );
                    } else {
                        $od = array(
                            'day'=>date('j', $order->exp_ship_date),
                            'order_length'=>$order->order_length,
                            'lacking_flg'=>$order->lacking_flg,
                        );
                    }
                    array_push($result->ship_date, $od);
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
                if($order->ship_date){
                    $od = array(
                        'day' => date('j', strtotime($order->ship_date)),
                        'order_length' => $order->order_length,
                        'lacking_flg' => $order->lacking_flg
                    );
                }else{
                    $od = array(
                        'day'=>date('j', strtotime($order->exp_ship_date)),
                        'order_length'=>$order->order_length,
                        'lacking_flg'=>$order->lacking_flg,
                    );
                }
                $tmpOrder->ship_date = array();
                array_push($tmpOrder->ship_date, $od);
                $tmpOrder->remarks = $order->remarks;
            }
            array_push($results, $tmpOrder);
        }

        return $results;

        
    }
}
