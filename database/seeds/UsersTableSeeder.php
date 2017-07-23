<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();

        User::create([
            'name'   => 'admin',
            'email' => 'admin@test.com',
            'password' => bcrypt(123456),
            'permission' => 'admin'
        ]);

        User::create([
            'name'   => 'user1',
            'email' => 'user1@test.com',
            'password' => bcrypt(123456)
        ]);

        User::create([
            'name'   => 'user2',
            'email' => 'user2@test.com',
            'password' => bcrypt(123456)
        ]);
    }
}
