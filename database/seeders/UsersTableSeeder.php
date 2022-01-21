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
        
        DB::table('users')->insert(
            [
                'name'       => 'superadmin',
                'email'      => 'bian@rectmedia.id',
                'password'   => bcrypt('superrect89'),
                'full_name'  => 'Murbianto',
                'no_hp'      => '089298390122',
                'user_role_id'=> 1,
            ],
           
        );
        DB::table('users')->insert(
            [
                'name'       => 'admin',
                'email'      => 'michaelmurbianto@gmail.com',
                'password'   => bcrypt('superrect89'),
                'full_name'  => 'Michael Agus',
                'no_hp'      => '087678129322',
                'user_role_id'=> 1,
            ],
           
        );
        // DB::table('users')->insert(
        //     [
        //         'name'       => 'admin',
        //         'email'      => 'admin_ps@gmail.com',
        //         'password'   => bcrypt('qwerty'),
        //         'full_name'  => 'Andi febianto',
        //         'no_hp'      => '08984036667',
        //         'user_role_id'=> 1,
        //     ],
           
        // );
    }
}
