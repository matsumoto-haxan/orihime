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

/* ---------- 注文画面からのAPIリクエスト ---------- */
// 注文一覧取得
Route::get('/order/search', 'ApiController@search')->name('order_search');
// 注文詳細取得
Route::get('/order/detail', 'ApiController@getDetail')->name('order_getDetail');

// 注文新規登録
Route::post('/order/create', 'ApiController@create')->name('order_create');
// 注文更新
Route::post('/order/update', 'ApiController@update')->name('order_update');
// 注文削除
Route::post('/order/delete', 'ApiController@delete')->name('order_delete');

// 会社一覧取得
Route::get('/order/companylist', 'ApiController@getCompanylist')->name('order_getCompanylist');
// 製品一覧取得
Route::get('/order/productlist', 'ApiController@getProductlist')->name('order_getProductlist');
// 発送予定日取得
Route::get('/order/expshipdate', 'ApiController@getExpshipdate')->name('order_getExpshipdate');


/* ---------- 注文管理画面からのAPIリクエスト ---------- */
// 会社一覧取得
Route::get('/management/companylist', 'ApiController@getCompanylist')->name('management_getCompanylist');
// 製品一覧取得
Route::get('/management/productlist', 'ApiController@getProductlist')->name('management_getProductlist');
// 注文一覧取得
Route::get('/management/search', 'ApiController@mngSearch')->name('management_search');


