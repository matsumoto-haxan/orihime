<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    
    public function index()
    {
        // 適当に変数に値を入れる
        $message = 'hello';
        $testarray = ['yeah', 'welcome!'];

        // compactメソッドに変数名をStringで渡すと勝手に送ってくれる
        return view('order', compact('message','testarray'));
    }
}
