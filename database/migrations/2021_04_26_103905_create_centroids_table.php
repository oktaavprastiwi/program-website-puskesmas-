<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCentroidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('centroids', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('file_id')->unsigned();
            $table->integer('transform_umur');
            $table->integer('transform_jenis_kelamin');
            $table->integer('transform_diagnosa');
            $table->integer('jumlah_penderita');
            $table->timestamps();

            $table->foreign('file_id')->references('id')->on('files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('centroids');
    }
}
