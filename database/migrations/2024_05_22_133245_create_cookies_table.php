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
        Schema::create('cookies', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cookie_group_id')->unsigned();
            $table->string('title');
            $table->string('provider');
            $table->json('purpose');
            $table->json('expiration');
            $table->string('type');
            $table->timestamps();
            $table->foreign('cookie_group_id')->references('id')
                ->on('cookie_groups')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cookies');
    }
};
