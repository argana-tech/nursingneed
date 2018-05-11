<?php

use Illuminate\Database\Seeder;
use App\User;

/*
 * php artisan db:seed --class=UserMastersTableSeeder
 */
class UserMastersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [1];

        foreach ($users as $id) {
            $user = User::where('id', '=', $id)->first();
            if ($user) {
                $user->firstImport();
            }
        }
    }
}
