<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('generated_codes', function (Blueprint $table) {
            $table->id();
            // Jika aplikasi Anda ada fitur login, uncomment baris di bawah:
            // $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('prompt');
            $table->longText('code'); // Menggunakan longText agar muat kode panjang
            $table->string('language', 50)->default('Unknown');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('generated_codes');
    }
};