<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class Export implements FromView
{

    private $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return $this->view;
    }
}


// use Maatwebsite\Excel\Concerns\FromCollection;

// class Export implements FromCollection
// {
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
        //
    // }
// }
