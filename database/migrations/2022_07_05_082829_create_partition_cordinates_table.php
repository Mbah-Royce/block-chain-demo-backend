<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartitionCordinatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partition_cordinates', function (Blueprint $table) {
            $table->id();
            $table->string('lat');
            $table->string('lng');
            $table->integer('array_position')->nullable();
            $table->unsignedBigInteger('partition_id');
            $table->foreign('partition_id')->references('id')->on('partitions');
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
        Schema::dropIfExists('partition_cordinates');
    }
}
