<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProjectMasterDt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_master_dt', function (Blueprint $table) {
            $table->increments('project_dt_id');
            $table->integer('project_id')->unsigned();
            $table->longText('image_detail');
            $table->string('discription')->nullable();
            $table->timestamps();
        });
        Schema::table('project_master_dt', function ($table) {
            $table->foreign('project_id')->references('project_id')->on('project_master')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_master_dt');
    }
}
