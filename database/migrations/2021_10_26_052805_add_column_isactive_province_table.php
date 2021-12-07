<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIsactiveProvinceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('province', function (Blueprint $table) {
            //
            $table->boolean('is_active')->nullable()->storedAs('CASE WHEN deleted_at IS NULL THEN 1 ELSE NULL END');
        });

        Schema::table('province', function (Blueprint $table) {
           
            $table->string('province_name')->change();
            $table->unique(['province_name','is_active']);
            
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
        // Schema::table('province', function (Blueprint $table) {
        //     $table->dropColumn('is_active');
        // });
    }
}
