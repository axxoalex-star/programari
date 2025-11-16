<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doctor_department', function (Blueprint $table) {
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('department_id');
            $table->primary(['doctor_id', 'department_id']);
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_department');
    }
};
