<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'email' => 'info@example.com',
                'password' => bcrypt('password'),
            ],
        ];

        foreach ($users as $u) {
            $user = User::where('email', '=', $u['email'])->first();
            if (!$user) {
                $user = new User();
            }

            foreach ($u as $k => $v) {
                $user->$k = $v;
            }
            $user->save();
        }
    }
}
