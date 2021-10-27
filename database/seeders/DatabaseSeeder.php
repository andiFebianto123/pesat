<?php

namespace Database\Seeders;

use App\Models\UserAttribute;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(UserRole::class);
        $this->call(UsersTableSeeder::class);
        $this->call(ProvinceTableSeeder::class);
        $this->call(CityTableSeeder::class);
        $this->call(ReligionTableSeeder::class);
        $this->call(StockStatusSeeder::class);
    }
}
