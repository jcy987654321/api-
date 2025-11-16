<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('otp_secret')->nullable()->after('password');
            $table->boolean('otp_enabled')->default(false)->after('otp_secret');
            $table->integer('failed_login_attempts')->default(0)->after('is_admin');
            $table->timestamp('account_locked_until')->nullable()->after('failed_login_attempts');
            $table->boolean('is_account_locked')->default(false)->after('account_locked_until');
            $table->integer('password_changed_at')->nullable()->after('is_account_locked');
            $table->text('password_history')->nullable()->after('password_changed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'otp_secret',
                'otp_enabled',
                'failed_login_attempts',
                'account_locked_until',
                'is_account_locked',
                'password_changed_at',
                'password_history',
            ]);
        });
    }
};
