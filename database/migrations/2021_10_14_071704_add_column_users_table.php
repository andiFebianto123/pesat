<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Nullable;

class AddColumnUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('full_name')->after('last_name');
            $table->string('hometown')->nullable()->after('full_name');
            $table->date('date_of_birth')->nullable()->after('hometown');
            $table->string('address')->nullable()->after('date_of_birth');
            $table->string('no_hp')->after('address');
            $table->string('church_member_of')->nullable()->after('no_hp');


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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('full_name');
            $table->dropColumn('hometown');
            $table->dropColumn('date_of_birth');
            $table->dropColumn('address');
            $table->dropColumn('no_hp');
            $table->dropColumn('church_member_of');
        });
    }
}
