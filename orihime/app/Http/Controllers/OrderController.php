<?php

namespace App\Http\Controllers;

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

    public function create()
    {
        return "create";
    }

    public function search()
    {
        return "search result";
    }
}
