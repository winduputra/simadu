<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('folders')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('unit_kerja_id')->nullable()->constrained('unit_kerjas')->nullOnDelete();
            $table->string('nama', 255);
            $table->text('deskripsi')->nullable();
            $table->string('color', 7)->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['parent_id', 'nama']);
            $table->index('user_id');
            $table->index('unit_kerja_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('folders');
    }
};
