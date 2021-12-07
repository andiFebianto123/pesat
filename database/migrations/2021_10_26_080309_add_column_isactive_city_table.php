<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIsactiveCityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('city', function (Blueprint $table) {
            
            $table->boolean('is_active')->nullable()->storedAs('CASE WHEN deleted_at IS NULL THEN 1 ELSE NULL END');
        });

        Schema::table('city', function (Blueprint $table) {
           
            $table->string('city_name')->change();
            $table->unique(['city_name','is_active']);
                  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        // Schema::table('city', function (Blueprint $table) {
        //     $table->dropColumn('is_active');
        // });
    }
}
