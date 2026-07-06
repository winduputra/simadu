<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->text('deskripsi')->nullable();
            $table->string('warna', 7)->nullable();
            $table->string('ikon', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_categories');
    }
};
