<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationTableUserAttributeToTableUsers extends Migration
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
    
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
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
        if(Schema::hasTable('user_attribute')){
            Schema::table('user_attribute', function (Blueprint $table) {
                //
                $table->dropForeign(['user_id']);
            });
        }
    }
}
