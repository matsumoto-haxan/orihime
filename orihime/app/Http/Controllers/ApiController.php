<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\Product;
use App\Company;
use App\Calendar;

/**
 * APIのコントローラクラス
 * 処理が簡単なものはこちらに突っ込んでいます
 * 処理が長いものはサービスクラスに突っ込んでいます
 */
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
        return app('OrderService')->getOrderList($request);
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

        $query = Product::query();
        $query->select('*')
        ->join('agreements', 'agreements.product_id', '=', 'products.id');
        if ($request->company_id != '') {
            $query->where('company_id', '=', $request->company_id);
        }
        return $query->get();
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
        $results = app('ManagementService')->getOrderList($request);
        return $results;
    }
}