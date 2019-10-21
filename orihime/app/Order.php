<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class Order extends Model
{
    use SoftDeletes;

    protected $dates = [
        'ship_date',
        'exp_ship_date',
        'delivery_date'
    ];
}
