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
        Schema::create('nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('version_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('nodes')->onDelete('cascade');

            $table->enum('type', ['folder', 'document']);
            $table->string('title');
            $table->string('slug'); // For URLs like cloud.md
            $table->string('path'); // Calculated path like general/installation/cloud
            $table->integer('order')->default(0);
            $table->boolean('is_root')->default(false);

            $table->timestamps();

            $table->index(['version_id', 'path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nodes');
    }
};
