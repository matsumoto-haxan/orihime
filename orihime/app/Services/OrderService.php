<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\Product;
use App\Company;
use App\Calendar;

class OrderService
{

    public function getOrderList(Request $request)
    {
        // ①DBから取得
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
        // date_default_timezone_set('Asia/Tokyo');
        $from = date_create($request->delivery_date);
        $to   = date_create($request->delivery_date);
        $to->modify('+1 months - 1 days');
        $query->whereBetween('delivery_date', [$from, $to]);

        $orders = $query->get();

        // ②レコードを製品・納品先毎に集約する
        $results      = array();
        $isDatePushed = 0;

        // 検索結果に対してループを回す
        foreach ($orders as $order) {
            $o_prd        = $order->product_id;
            $o_cmp        = $order->company_id;
            $isDatePushed = 0;
            $tmpOrder     = (object) array();

            // 返却データに対してループを回して確認
            foreach ($results as $result) {
                $r_prd = $result->product_id;
                $r_cmp = $result->company_id;

                // すでに返却データに登録されていた場合
                if ($o_prd == $r_prd && $o_cmp == $r_cmp) {

                    $dayStr = date('j', strtotime($order->delivery_date));

                    // 登録済みの1~31を回す
                    foreach ($result->delivery_date as &$dd) {
                        // 該当する日付の配列を検索結果の情報で上書き
                        if ($dd['day'] == $dayStr) {
                            $dd['order_id']     = $order->order_id;
                            $dd['order_length'] = $order->order_length;
                            $dd['lacking_flg']  = $order->lacking_flg;
                            break;
                        }
                    }
                    $isDatePushed += 1;
                    break;
                }
            }

            // もし返却データに登録されていなければ、1レコード追加
            if ($isDatePushed == 0) {
                $tmpOrder->product_id    = $order->product_id;
                $tmpOrder->company_id    = $order->company_id;
                $tmpOrder->product_code  = $order->product_code;
                $tmpOrder->delivery_name = $order->delivery_name;

                $tmpOrder->delivery_date = array();
                $dayStr                  = date('j', strtotime($order->delivery_date));

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
                    if ($dayStr == $i) {

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
