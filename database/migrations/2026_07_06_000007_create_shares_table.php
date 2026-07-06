<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shares', function (Blueprint $table) {
            $table->id();
            $table->string('shareable_type');
            $table->unsignedBigInteger('shareable_id');
            $table->foreignId('shared_by')->constrained('users')->restrictOnDelete();
            $table->string('shared_to_type');
            $table->unsignedBigInteger('shared_to_id');
            $table->enum('permission', ['viewer', 'editor', 'manager']);
            $table->timestamps();

            $table->index(['shareable_type', 'shareable_id']);
            $table->index(['shared_to_type', 'shared_to_id']);
            $table->index('shared_by');
            $table->unique(['shareable_type', 'shareable_id', 'shared_to_type', 'shared_to_id'], 'shares_unique_combo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shares');
    }
};
