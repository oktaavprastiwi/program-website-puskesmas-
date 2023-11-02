<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transformations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('file_id')->unsigned();
            $table->string('umur');
            $table->string('jenis_kelamin');
            $table->string('nama_penyakit');
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
        Schema::dropIfExists('transformations');
    }
}
