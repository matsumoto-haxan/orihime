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

// 注文検索
// TODO: とりあえずGETで作成。疎通後POSTに変更
Route::get('/order/search', 'OrderController@search')->name('order_search');


// 注文新規登録
// TODO: とりあえずGETで作成。疎通後POSTに変更
Route::get('/order/create', 'OrderController@create')->name('order_create');


