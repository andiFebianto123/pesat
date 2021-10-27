<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFromUserAttributeToTableSponsorMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sponsor_master', function (Blueprint $table) {
            //
            $table->string('website_url')->nullable()->after('password');
            $table->string('facebook_url')->nullable()->after('website_url');
            $table->string('instagram_url')->nullable()->after('facebook_url');
            $table->string('linkedin_url')->nullable()->after('instagram_url');
            $table->string('my_space_url')->nullable()->after('linkedin_url');
            $table->string('pinterest_url')->nullable()->after('my_space_url');
            $table->string('sound_cloud_url')->nullable()->after('pinterest_url');
            $table->string('tumblr_url')->nullable()->after('sound_cloud_url');
            $table->string('twitter_url')->nullable()->after('tumblr_url');
            $table->string('youtube_url')->nullable()->after('twitter_url');
            $table->string('biograpical')->nullable()->after('youtube_url');
            $table->string('photo_profile')->nullable()->after('biograpical');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sponsor_master', function (Blueprint $table) {
            //
            $table->dropColumn('website_url');
            $table->dropColumn('facebook_url');
            $table->dropColumn('instagram_url');
            $table->dropColumn('linkedin_url');
            $table->dropColumn('my_space_url');
            $table->dropColumn('pinterest_url');
            $table->dropColumn('sound_cloud_url');
            $table->dropColumn('tumblr_url');
            $table->dropColumn('twitter_url');
            $table->dropColumn('youtube_url');
            $table->dropColumn('biograpical');
            $table->dropColumn('photo_profile');
        });
    }
}
