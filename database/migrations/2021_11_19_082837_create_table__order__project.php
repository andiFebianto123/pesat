<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOrderProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_project', function (Blueprint $table) {
            $table->increments('order_project_id');
            $table->string('order_project_no',16);
            $table->integer('sponsor_id')->unsigned();
            $table->integer('project_id')->unsigned();
            $table->decimal('price',10,2);
            $table->enum('payment_status', ['1', '2', '3', '4'])->comment('1=menunggu pembayaran, 2=sudah dibayar, 3=kadaluarsa, 4=batal');
            $table->string('snap_token', 36)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('order_project', function (Blueprint $table) {
            
            $table->foreign('sponsor_id')->references('sponsor_id')->on('sponsor_master')->onDelete('restrict')->onUpdate('cascade');
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
        Schema::table('order_project', function (Blueprint $table) {
            
            $table->dropForeign(['sponsor_id']);
            $table->dropForeign(['project_id']);
        });
        Schema::dropIfExists('order_project');
    }
}
