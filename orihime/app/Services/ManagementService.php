<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\Product;
use App\Company;
use App\Calendar;

class ManagementService
{
    public function exampleFunc()
    {
        return 1;
    }

    /**
     * 注文を検索して整形して返すメソッド
     * 一覧表示とPDF出力、どちらにも使う想定
     * TODO: 引数はRequestじゃなくて何某かのモデルにした方がいいのかもしれない…
     */
    public function getOrderList(Request $request)
    {
        /*
         ①DBから取得 
         */
        $query = Order::query();
        $query->select('*')
            ->addSelect('orders.id as order_id')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->join('companies', 'orders.company_id', '=', 'companies.id');

        // 会社情報の検索条件を追加
        if ($request->company_id) {
            $query->where('company_id', '=', $request->company_id);
        } else if ($request->customer_code) {
            $query->where('customer_code', '=', $request->customer_code);
        }

        // 製品情報の検索条件を追加
        if ($request->product_id) {
            $query->where('product_id', '=', $request->product_id);
        } else {
            if ($request->product_code) {
                $query->where('product_code', '=', $request->product_code);
                if ($request->material_code) {
                    $query->where('material_code', '=', $request->material_code);
                }
            }
        }

        // 納品日の期間を検索条件に追加
        $from = date_create($request->delivery_date);
        $to   = date_create($request->delivery_date);
        $to->modify('+1 months - 1 minutes');
        $query->whereBetween('delivery_date', [$from, $to]);

        // ソート条件を追加
        $query->orderBy('product_code', 'asc')
            ->orderBy('delivery_code', 'asc')
            ->orderBy('delivery_date', 'asc');

        // 検索実行
        $orders = $query->get();


        /*
         ②レコードを製品・納品先毎に集約する
         */
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


        /*
         ③検索結果に対してループを回す
         */
        foreach ($orders as $order) {

            // 結果セットの到着日を取得
            $dlvDate = date_create($order->delivery_date)->format('d');
            // 結果セットの発送予定日を整形
            $expShipDate = date_create($order->exp_ship_date)->format('m/d');


            // 品番・配送先が前回のループと同じ場合
            if($currentProductId == $order->product_id 
            && $currentDeliveryId == $order->delivery_id){

                // 結果セットの最後のKeyを取得
                $key = array_key_last($results);


                // 該当する配送日の位置に挿入
                $results[$key]['exp_ship_date'][$dlvDate - 1] = $expShipDate;
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
                $tmpList['exp_ship_date'][$dlvDate - 1] = $expShipDate;
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
