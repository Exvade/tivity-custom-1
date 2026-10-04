<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_name', 70);
            $table->enum('attendance', ['yes', 'no']);
            $table->unsignedTinyInteger('guest_count')->default(1);
            $table->text('message');
            $table->enum('status', ['pending', 'approved', 'rejected', 'hidden'])->default('pending');
            $table->string('moderation_reason')->nullable();
            $table->string('ip_hash', 64)->nullable()->index();
            $table->string('user_agent', 500)->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['invitation_id', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishes');
    }
};
