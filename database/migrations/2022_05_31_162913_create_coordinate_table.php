<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoordinateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coordinate', function (Blueprint $table) {
            $table->id();
            $table->string('lat');
            $table->string('lng');
            $table->unsignedBigInteger('land_certificate_id');
            $table->foreign('land_certificate_id')->references('id')->on('land_certificates');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coordinate');
    }
}
