<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Auth::routes();

// ダッシュボード（ホーム画面）
Route::get('/home', 'HomeController@index')->name('home');

// 注文入力画面
Route::get('/order', 'OrderController@index')->name('order');

// 出荷管理画面
Route::get('/management', 'ManagementController@index')->name('management');
Route::get('/management/export', 'ManagementController@export')->name('exporttest');

// 出荷指示書画面
Route::get('/instruction', 'InstructionController@index')->name('instruction');

