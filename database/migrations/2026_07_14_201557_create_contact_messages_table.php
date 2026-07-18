<?php
// database/migrations/2024_01_01_000026_create_contact_messages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('subject');
            $table->text('message');
            $table->enum('category', ['project_inquiry', 'support', 'partnership', 'other'])->default('other');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', ['unread', 'read', 'replied', 'archived', 'spam'])->default('unread');
            $table->text('reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->foreignId('replied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('attachments')->nullable();
            $table->string('ip_address')->nullable();
            $table->boolean('is_spam')->default(false);
            $table->timestamps();
            
            $table->index(['status', 'is_spam']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};