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
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'household_id')) {
                $table->dropForeign(['household_id']);
                $table->dropColumn('household_id');
            }
        });

        Schema::create('household_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('household_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unique(['household_id', 'user_id']);

            $table->enum('role', ['owner', 'member'])->default('member');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('household_user');
    }
};
