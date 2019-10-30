<?php

use Illuminate\Database\Seeder;
use App\Calendar;


class CalendarsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $week_name = array("日", "月", "火", "水", "木", "金", "土");
        $refDate = new DateTime('2019-11-01 00:00:00');

        for($i = 1; $i <= 60; $i++){

            $item1              = new Calendar();
            $item1->date        = $refDate;
            $item1->year        = $refDate->format('Y');
            $item1->month       = $refDate->format('n');
            $item1->day         = $refDate->format('j');
            $item1->weekday     = $week_name[$refDate->format('w')];
            if($refDate->format('w')==0 || $refDate->format('w')==1){
                $item1->holiday_flg = 1;
            } else{
                $item1->holiday_flg = 0;
            }
            
            $item1->sort_order  = '0';
            $item1->save();

            $refDate->modify('+1 days');
        }
    }
}
