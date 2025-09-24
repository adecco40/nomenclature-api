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
        Schema::create('change_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('entity_type');
            $table->uuid('entity_id');
            $table->string('action');
            $table->jsonb('changes')->nullable();
            $table->timestamp('created_at');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('change_history');
    }
};