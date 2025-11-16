<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->boolean('receive_next_day_email')->default(false)->after('is_active');
            $table->unsignedTinyInteger('next_day_email_hour')->default(7)->after('receive_next_day_email');
            $table->string('notification_email')->nullable()->after('next_day_email_hour');
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['receive_next_day_email', 'next_day_email_hour', 'notification_email']);
        });
    }
};
