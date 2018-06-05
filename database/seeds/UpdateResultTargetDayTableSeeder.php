<?php

use Illuminate\Database\Seeder;
use App\ResultTargetDay;

/*
 * php artisan db:seed --class=UpdateResultTargetDayTableSeeder
 */
class UpdateResultTargetDayTableSeeder extends Seeder
{
    public function run()
    {
        $resultTargetDays = ResultTargetDay::all();

        foreach ($resultTargetDays as $resultTargetDay) {
            $resultTargetDay->h_ward = ltrim_zero($resultTargetDay->h_ward);
            $resultTargetDay->ef_ward = ltrim_zero($resultTargetDay->ef_ward);
            $resultTargetDay->save();
        }
    }
}
