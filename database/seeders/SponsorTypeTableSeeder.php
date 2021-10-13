<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SponsorTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::insert('INSERT INTO sponsor_type (sponsor_type_id, sponsor_type_name) VALUES
        (1, "Uncategorized"),
        (2, "Anak"),
        (3, "Proyek")'
        );
    }
}
