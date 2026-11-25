<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('original_name', 255);
            $table->string('stored_name', 255);
            $table->string('disk', 50)->default('private');
            $table->string('path', 500);
            $table->string('mime_type', 150);
            $table->string('extension', 20);
            $table->unsignedBigInteger('size');
            $table->char('checksum', 64);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['task_id', 'deleted_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_attachments');
    }
};
