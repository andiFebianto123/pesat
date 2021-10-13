<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReligionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::insert('INSERT INTO religion (religion_id, religion_name) VALUES
        (1, "Islam"),
        (2, "Kristen"),
        (3, "Katolik"),
        (4, "Hindu"),
        (5, "Budha"),
        (6, "Khonghucu")'
        );
    }
}
