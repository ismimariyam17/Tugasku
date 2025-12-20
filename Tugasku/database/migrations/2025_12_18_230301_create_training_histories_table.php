<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('training_histories', function (Blueprint $table) {
            $table->id();
            $table->string('model_type'); // CNN / ANN
            $table->integer('epochs');
            $table->float('accuracy');
            $table->float('loss');
            $table->string('plot_file')->nullable(); // Lokasi gambar grafik
            $table->string('model_file')->nullable(); // Lokasi file .h5
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_histories');
    }
};
