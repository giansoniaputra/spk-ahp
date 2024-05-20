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
        Schema::create('perbandingan_sub_kriteria', function (Blueprint $table) {
            $table->id();
            $table->uuid('unique')->unique();
            $table->foreignId('sub_kriteria1_id')->constrained('sub_kriteria')->onDelete('cascade');
            $table->foreignId('sub_kriteria2_id')->constrained('sub_kriteria')->onDelete('cascade');
            $table->foreignId('kriteria_id')->constrained('kriteria')->onDelete('cascade');
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
        Schema::dropIfExists('perbandingan_sub_kriteria');
    }
};
