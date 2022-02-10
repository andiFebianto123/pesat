<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Config::updateOrCreate([
            'key' => 'Administration Email Address'
        ], [
            'key' => 'Administration Email Address',
            'value' => 'kevin@rectmedia.id'
        ]);
    }
}
