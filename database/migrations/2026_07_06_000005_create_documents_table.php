<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->nullable()->constrained('folders')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('document_category_id')->nullable()->constrained('document_categories')->nullOnDelete();
            $table->string('nama', 255);
            $table->string('nama_file_asli', 500);
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('ukuran');
            $table->string('storage_path', 1000);
            $table->unsignedInteger('current_version')->default(1);
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('folder_id');
            $table->index('user_id');
            $table->index('document_category_id');
            $table->index('mime_type');
            if (config('database.default') !== 'sqlite') {
                $table->fullText(['nama', 'nama_file_asli']);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
