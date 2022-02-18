<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnInChildMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('child_master', function (Blueprint $table) {
            $table->dropForeign('child_master_city_id_foreign');
            $table->dropIndex('child_master_city_id_foreign');
            $table->integer('city_id')->unsigned()->nullable()->change();
            $table->foreign('city_id')->nullable()->references('city_id')->on('city');

            $table->dropForeign('child_master_hometown_foreign');
            $table->dropIndex('child_master_hometown_foreign');
            $table->integer('hometown')->unsigned()->nullable()->change();
            $table->foreign('hometown')->nullable()->references('city_id')->on('city');

            $table->dropForeign('child_master_religion_id_foreign');
            $table->dropIndex('child_master_religion_id_foreign');
            $table->integer('religion_id')->unsigned()->nullable()->change();
            $table->foreign('religion_id')->nullable()->references('religion_id')->on('religion');

            $table->dropForeign('child_master_province_id_foreign');
            $table->dropIndex('child_master_province_id_foreign');
            $table->integer('province_id')->unsigned()->nullable()->change();
            $table->foreign('province_id')->nullable()->references('province_id')->on('province');

            $table->string('nickname')->nullable()->change();
            $table->string('father')->nullable()->change();
            $table->string('mother')->nullable()->change();
            $table->string('profession')->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->string('economy')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
