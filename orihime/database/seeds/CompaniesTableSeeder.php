<?php

use Illuminate\Database\Seeder;
use App\Company;


class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $item1                  = new Company();
        $item1->customer_name   = '帝人フロンティア';
        $item1->customer_code   = '2A2C';
        $item1->delivery_name   = '東洋クオリティワン【コイワ内】';
        $item1->delivery_code   = '3491-26T';
        $item1->enduser_name    = '林テレンプ';
        $item1->enduser_code    = '';
        $item1->delivery_lag    = '3';
        $item1->company_remarks = '受け取り担当者は「山田」様です';
        $item1->save();

        $item2                  = new Company();
        $item2->customer_name   = '帝人フロンティア';
        $item2->customer_code   = '2A2C';
        $item2->delivery_name   = '東名化成（株）三重';
        $item2->delivery_code   = '5493';
        $item2->enduser_name    = '林テレンプ';
        $item2->enduser_code    = '';
        $item2->delivery_lag    = '1';
        $item2->company_remarks = '';
        $item2->save();

        $item3                  = new Company();
        $item3->customer_name   = 'セージ・オートモーティブ・インテリア';
        $item3->customer_code   = 'SAI01';
        $item3->delivery_name   = '田島縫製（鈴鹿事業所）';
        $item3->delivery_code   = 'th001';
        $item3->enduser_name    = '林テレンプ';
        $item3->enduser_code    = '';
        $item3->delivery_lag    = '2';
        $item3->company_remarks = '';
        $item3->save();

    }
}
