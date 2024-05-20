<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kriteria_s_a_w_s', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->integer('kode');
            $table->string('kriteria');
            $table->string('atribut');
            $table->float('bobot');
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
        Schema::dropIfExists('kriteria_s_a_w_s');
    }
};
