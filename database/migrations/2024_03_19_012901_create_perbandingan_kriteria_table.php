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
        Schema::create('perbandingan_kriteria', function (Blueprint $table) {
            $table->id();
            $table->uuid('unique')->unique();
            $table->integer('kriteria1_id')->constrained('kriteria')->onDelete('cascade');
            $table->integer('kriteria2_id')->constrained('kriteria')->onDelete('cascade');
            $table->integer('value');
            $table->float('nilai_perbandingan');
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
        Schema::dropIfExists('prebandingan_kriteria');
    }
};
