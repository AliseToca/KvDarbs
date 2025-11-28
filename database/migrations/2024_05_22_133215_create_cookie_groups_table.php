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
        Schema::create('cookie_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('title');
            $table->json('description')->nullable();
            $table->integer('position')->nullable();
            $table->boolean('active')->nullable();
            $table->boolean('enabled_by_default')->nullable();
            $table->boolean('is_mandatory')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cookie_groups');
    }
};
