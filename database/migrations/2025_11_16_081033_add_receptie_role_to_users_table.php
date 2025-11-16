<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify enum to add 'receptie' role
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'doctor', 'assistant', 'receptie') NOT NULL DEFAULT 'assistant'");

        Schema::table('users', function (Blueprint $table) {
            // Add clinic association for receptie users
            $table->foreignId('appointment_type_id')->nullable()->after('doctor_id')->constrained('appointment_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['appointment_type_id']);
            $table->dropColumn('appointment_type_id');
        });

        // Revert enum to original values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'doctor', 'assistant') NOT NULL DEFAULT 'assistant'");
    }
};
