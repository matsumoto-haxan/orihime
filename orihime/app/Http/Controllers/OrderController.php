<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


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
        $this->middleware('auth');
    }

    /**
     * 通常遷移
     */
    public function index()
    {
        return view('order');   
    }


    /*
    不要？
    function getCompany(?string $customer_code, ?string $delivery_code, ?string $enduser_code)
    {
        $company = Company::
            where('customer_code', '=', $customer_code)
            ->where('delivery_code', '=', $delivery_code)
            ->where('enduser_code', '=', $enduser_code)
            ->first();
        return $company;
    }

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
     */

}
