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
    public function export()
    {
        $calendars = Calendar::get();

        $view  = \view('exporttest', compact('calendars'));
        return \Excel::download(new Export($view), 'exp.pdf');
    }
}
