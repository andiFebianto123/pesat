<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProjectMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_master', function (Blueprint $table) {
            $table->increments('project_id');
            $table->integer('sponsor_type_id')->unsigned();
            $table->string('title');
            $table->longText('discription');
            $table->longText('featured_image');
            $table->boolean('status')->default(1);
            $table->bigInteger('created_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('project_master', function ($table) {
            $table->foreign('sponsor_type_id')->references('sponsor_type_id')->on('sponsor_type')->onDelete('restrict')->onUpdate('cascade');
        });
        Schema::table('project_master', function ($table) {
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_master');
    }
}
