<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habit_id')->constrained()->cascadeOnDelete();
            $table->date('completed_on');
            $table->timestamps();
            $table->unique(['habit_id', 'completed_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('completions');
    }
};
