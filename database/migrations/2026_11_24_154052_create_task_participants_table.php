<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_participants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('added_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('added_at');
            $table->foreignId('removed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->dateTime('removed_at')->nullable();
            $table->timestamps();
            $table->unique(['task_id', 'user_id']);
            $table->index(['user_id', 'removed_at']);
            $table->index(['task_id', 'removed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_participants');
    }
};
