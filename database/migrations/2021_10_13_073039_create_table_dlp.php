<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDlp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dlp', function (Blueprint $table) {
            $table->increments('dlp_id');
            $table->integer('child_id')->unsigned();
            $table->string('file_dlp')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('dlp', function ($table) {
            $table->foreign('child_id')->references('child_id')->on('child_master')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dlp');
    }
}
