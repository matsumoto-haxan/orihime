<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\Export;
use App\Calendar;



class ManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('management');
    }




















    

    /**
     * エクセル出力テスト
     */
    public function export_()
    {
        $calendars = Calendar::get();

        $view  = \view('exporttest', compact('calendars'));
        return \Excel::download(new Export($view), 'exp.xlsx');
    }

    /**
     * PDF出力テスト
     */
    public function export()
    {
        $calendars = Calendar::get();

        $view  = \view('exporttest', compact('calendars'));

        // $pdf = \PDF::loadHTML($view);
        $pdf = \PDF::loadHTML($view)->setPaper('b4', 'landscape'); 


        // ブラウザにPDFを直接表示させたい場合
        return $pdf->stream('title.pdf');

        // ダウンロードさせたい場合
        // return $pdf->download('title.pdf');


    }
}
