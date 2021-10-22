<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::insert('INSERT INTO stock_status (stock_status_id, stock_status_name) VALUES
        (1, "Instock"),
        (2, "Tersponsori"),
        (3, "On backorder")'
        );
    }
}
