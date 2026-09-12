<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_apis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('driver');
            $table->string('method')->default('GET');
            $table->string('url');
            $table->string('user_agent')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_apis');
    }
};
