<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAndRelationUserAttributeTable extends Migration
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
                $table->integer('sponsor_id')->unsigned()->nullable()->after('user_id');
            });

            Schema::table('user_attribute', function ($table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            });
            Schema::table('user_attribute', function ($table) {
                $table->foreign('sponsor_id')->references('sponsor_id')->on('sponsor_master')->onDelete('restrict')->onUpdate('cascade');
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
                $table->dropColumn('sponsor_id');
            });
            Schema::table('user_attribute', function (Blueprint $table) {
                //
                $table->dropForeign(['user_id']);
            });
            Schema::table('user_attribute', function (Blueprint $table) {
                //
                $table->dropForeign(['sponsor_id']);
            });
        }
        
    }
}
