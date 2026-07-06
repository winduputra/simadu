<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_links', function (Blueprint $table) {
            $table->id();
            $table->string('linkable_type');
            $table->unsignedBigInteger('linkable_id');
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('token', 64)->unique();
            $table->enum('permission', ['viewer', 'editor'])->default('viewer');
            $table->boolean('has_password')->default(false);
            $table->string('password')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('max_access_count')->nullable();
            $table->unsignedInteger('access_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['linkable_type', 'linkable_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_links');
    }
};
