<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    protected $dates = [
        'ship_date',
        'exp_ship_date',
        'delivery_date'
    ];
}
