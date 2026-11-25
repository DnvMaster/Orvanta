<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_reminder_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->restrictOnDelete();
            $table->string('reminder_type', 30);
            $table->dateTime('due_at_snapshot');
            $table->dateTime('scheduled_for');
            $table->dateTime('sent_at');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['task_id', 'recipient_id', 'reminder_type', 'due_at_snapshot', 'scheduled_for'], 'task_reminder_deliveries_unique');
            $table->index(['task_id', 'due_at_snapshot']);
            $table->index(['recipient_id', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_reminder_deliveries');
    }
};
