<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_mail_preferences', function (Blueprint $table) {
            $table->timestamp('marketing_opted_in_at')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_mail_preferences', function (Blueprint $table) {
            $table->dropColumn('marketing_opted_in_at');
        });
    }
};
