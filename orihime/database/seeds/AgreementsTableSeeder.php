<?php

use Illuminate\Database\Seeder;
use App\Agreement;


class AgreementsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Agreement::truncate();

        $ag1             = new Agreement();
        $ag1->company_id = '1';
        $ag1->product_id = '1';
        $ag1->save();

        $ag2             = new Agreement();
        $ag2->company_id = '2';
        $ag2->product_id = '1';
        $ag2->save();

        $ag3             = new Agreement();
        $ag3->company_id = '1';
        $ag3->product_id = '2';
        $ag3->save();

        $ag4             = new Agreement();
        $ag4->company_id = '1';
        $ag4->product_id = '3';
        $ag4->save();

        $ag5             = new Agreement();
        $ag5->company_id = '1';
        $ag5->product_id = '4';
        $ag5->save();

        $ag6             = new Agreement();
        $ag6->company_id = '1';
        $ag6->product_id = '5';
        $ag6->save();

        
    }
}
