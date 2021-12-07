<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyColumnRegistrationNumberAndAddColumnIsactiveChildMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::table('child_master', function (Blueprint $table) {
            
            $table->boolean('is_active')->nullable()->storedAs('CASE WHEN deleted_at IS NULL THEN 1 ELSE NULL END');
        });
        Schema::table('child_master', function (Blueprint $table) {
          
            $table->unique(['registration_number','is_active']);
                  
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
        // Schema::table('child_master', function (Blueprint $table) {
        //     $table->dropColumn('is_active');
        // });
    }
}
