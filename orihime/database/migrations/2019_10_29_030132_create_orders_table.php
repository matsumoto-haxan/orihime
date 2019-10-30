<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('product_id');
            $table->integer('company_id');
            $table->string('opt_order_no');
            $table->date('delivery_date');
            $table->date('exp_ship_date');
            $table->date('ship_date');
            $table->integer('roll_amount');
            $table->integer('order_length');
            $table->integer('result_length');
            $table->integer('lacking_flg');
            $table->string('remarks');
            $table->integer('user_id');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
