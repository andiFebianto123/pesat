<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropRelationUserAttributeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('user_attribute')){
            Schema::table('user_attribute', function (Blueprint $table) {
                //
                $table->dropForeign(['user_id']);
            });
            Schema::table('user_attribute', function (Blueprint $table) {
               
                $table->bigInteger('user_id')->unsigned()->nullable()->change();
                      
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
