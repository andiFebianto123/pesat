<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('user_attribute')->insert(
            [
                'user_id'       => 1,
            ],
           
        );
        DB::table('user_attribute')->insert(
            [
                'user_id'       => 2,
            ],
           
        );
    }
}
