<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->string('client_email');
            $table->string('client_name');
            $table->string('client_phone')->nullable();
            $table->string('title');
            $table->longText('notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
