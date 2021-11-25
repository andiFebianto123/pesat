<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRole extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('user_role')->insert(
            [
                'user_role_name'       => 'Administrator',
            ],
           
        );
        DB::table('user_role')->insert(
            [
                'user_role_name'       => 'Admin Pusat',
            ],
           
        );
        DB::table('user_role')->insert(
            [
                'user_role_name'       => 'Admin Daerah',
            ],
           
        );
        DB::table('user_role')->insert(
            [
                'user_role_name'       => 'Teknis',
            ],
           
        );
        DB::table('user_role')->insert(
            [
                'user_role_name'       => 'Media',
            ],
           
        );
    }
}
