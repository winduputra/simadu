<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('versi');
            $table->string('nama_file', 500);
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('ukuran');
            $table->string('storage_path', 1000);
            $table->text('catatan')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->unique(['document_id', 'versi']);
            $table->index('document_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};
