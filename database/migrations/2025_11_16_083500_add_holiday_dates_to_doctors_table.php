<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            if (!Schema::hasColumn('doctors', 'holiday_dates')) {
                $table->json('holiday_dates')->nullable()->after('notification_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            if (Schema::hasColumn('doctors', 'holiday_dates')) {
                $table->dropColumn('holiday_dates');
            }
        });
    }
};
