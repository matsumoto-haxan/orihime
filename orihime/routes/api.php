<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// 注文一覧取得
Route::get('/order/search', 'OrderController@search')->name('order_search');
// 注文詳細取得
Route::get('/order/detail', 'OrderController@getDetail')->name('order_getDetail');

// 会社一覧取得
Route::get('/order/companylist', 'OrderController@getCompanylist')->name('order_getCompanylist');
// 製品一覧取得
Route::get('/order/productlist', 'OrderController@getProductlist')->name('order_getProductist');
// 発送予定日取得
Route::get('/order/expshipdate', 'OrderController@getExpshipdate')->name('order_getExpshipdate');


// 注文新規登録
Route::post('/order/create', 'OrderController@create')->name('order_create');
// 注文更新
Route::post('/order/update', 'OrderController@update')->name('order_update');
// 注文削除
Route::post('/order/delete', 'OrderController@delete')->name('order_delete');



