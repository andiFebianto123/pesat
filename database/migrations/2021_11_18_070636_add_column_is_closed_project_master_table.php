<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIsClosedProjectMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('project_master', function (Blueprint $table) {
            //
            $table->date('start_date')->after('featured_image');
            $table->date('end_date')->after('start_date');
            $table->decimal('amount',10,2)->after('end_date');
            $table->boolean('is_closed')->default(0)->after('amount');
            $table->decimal('last_amount',10,2)->nullable()->after('is_closed');

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
        Schema::table('project_master', function (Blueprint $table) {
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->dropColumn('amount');
            $table->dropColumn('is_closed');
            $table->dropColumn('last_amount');
        });
    }
}
