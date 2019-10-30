<?php

use Illuminate\Database\Seeder;

class AgreementsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ag1             = new Agreement();
        $ag1->company_id = '1';
        $ag1->product_id = '1';
        $ag1->save();

        $ag2             = new Agreement();
        $ag2->company_id = '2';
        $ag2->product_id = '1';
        $ag2->save();

        /*
        $ag3             = new Agreement();
        $ag3->company_id = '';
        $ag3->product_id = '';
        $ag3->save();

        $ag4             = new Agreement();
        $ag4->company_id = '';
        $ag4->product_id = '';
        $ag4->save();
        */
        
    }
}
