<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->unsignedTinyInteger('max_submissions')->default(3);
            $table->unsignedTinyInteger('submission_count')->default(0);
            $table->timestamp('last_submitted_at')->nullable();
            $table->timestamps();
            $table->index(['invitation_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
