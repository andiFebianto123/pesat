<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('users')->insert(
            [
                'name'       => 'superadmin',
                'email'      => 'bian@rectmedia.id',
                'password'   => bcrypt('superrect89'),
                'user_role_id'=> 1,
            ],
           
        );
        DB::table('users')->insert(
            [
                'name'       => 'admin',
                'email'      => 'michaelmurbianto@gmail.com',
                'password'   => bcrypt('superrect89'),
                'user_role_id'=> 1,
            ],
           
        );
    }
}
