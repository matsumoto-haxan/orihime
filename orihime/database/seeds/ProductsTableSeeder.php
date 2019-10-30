<?php

use Illuminate\Database\Seeder;
use App\Product;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $item1                  = new Product();
        $item1->product_code   = 'TR640A';
        $item1->material_code   = '';
        $item1->color_code   = 'Y605';
        $item1->roll_length   = '50';
        $item1->save();

        $item2                = new Product();
        $item2->product_code  = 'TR640AW';
        $item2->material_code = '';
        $item2->color_code    = 'Y605';
        $item2->roll_length   = '50';
        $item2->save();

        $item3                = new Product();
        $item3->product_code  = 'TR662A';
        $item3->material_code = 'row_suede_001';
        $item3->color_code    = 'blk96';
        $item3->roll_length   = '40';
        $item3->save();

        $item4                = new Product();
        $item4->product_code  = 'TR662A';
        $item4->material_code = 'row_suede_001';
        $item4->color_code    = 'red34';
        $item4->roll_length   = '40';
        $item4->save();

        $item5                = new Product();
        $item5->product_code  = 'TR662A';
        $item5->material_code = 'row_suede_002';
        $item5->color_code    = 'wht503';
        $item5->roll_length   = '40';
        $item5->save();

    }
}
