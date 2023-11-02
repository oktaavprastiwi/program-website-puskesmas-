<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiagnosisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diagnosis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('file_id')->unsigned();
            $table->date('tanggal');
            $table->string('no_index');
            $table->string('kode_penyakit');
            $table->string('diagnosa');
            $table->string('alamat');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->integer('umur');
            $table->string('unit');
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
        Schema::dropIfExists('diagnosis');
    }
}
