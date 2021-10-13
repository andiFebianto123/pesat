<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableChildMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('child_master', function (Blueprint $table) {
                $table->increments('child_id');
                $table->string('registration_number');
                $table->string('full_name');
                $table->string('nickname');
                $table->integer('sponsor_type_id')->unsigned();
                $table->string('gender');
                $table->integer('hometown')->unsigned();
                $table->date('date_of_birth');
                $table->integer('religion_id')->unsigned();
                $table->string('fc')->nullable();
                $table->string('sponsor_name')->nullable();
                $table->integer('city_id')->unsigned();
                $table->string('districts');
                $table->integer('province_id')->unsigned();
                $table->string('father');
                $table->string('mother');
                $table->string('profession');
                $table->string('economy');
                $table->string('class');
                $table->string('school');
                $table->string('school_year');
                $table->date('sign_in_fc')->nullable();
                $table->date('leave_fc')->nullable();
                $table->string('reason_to_leave')->nullable();
                $table->string('child_discription')->nullable();
                $table->string('internal_discription')->nullable();
                $table->longText('photo_profile')->nullable();
                $table->string('file_profile')->nullable();
                $table->boolean('status')->default(1)->change();;
                $table->boolean('dlp_status')->default(0)->change();;
                $table->bigInteger('created_by')->unsigned();
                $table->bigInteger('updated_by')->unsigned();
                $table->timestamps();
                $table->softDeletes();
            });
            Schema::table('child_master', function ($table) {
                $table->foreign('sponsor_type_id')->references('sponsor_type_id')->on('sponsor_type')->onDelete('restrict')->onUpdate('cascade');
            });
            Schema::table('child_master', function ($table) {
                $table->foreign('hometown')->references('city_id')->on('city')->onDelete('restrict')->onUpdate('cascade');
            });
            Schema::table('child_master', function ($table) {
                $table->foreign('religion_id')->references('religion_id')->on('religion')->onDelete('restrict')->onUpdate('cascade');
            });
    
            Schema::table('child_master', function ($table) {
                $table->foreign('city_id')->references('city_id')->on('city')->onDelete('restrict')->onUpdate('cascade');
            });
            Schema::table('child_master', function ($table) {
                $table->foreign('province_id')->references('province_id')->on('province')->onDelete('restrict')->onUpdate('cascade');
            });
            Schema::table('child_master', function ($table) {
                $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            });
            Schema::table('child_master', function ($table) {
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('child_master');
    }
}
