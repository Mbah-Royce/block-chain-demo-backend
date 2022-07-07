<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeoCoordinates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('land_certificates', function (Blueprint $table) {
            $table->string("location")->nullable()->change();
            $table->string('feature_id');
            $table->string('feature_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('land_certificates', function (Blueprint $table) {
            //
        });
    }
}
