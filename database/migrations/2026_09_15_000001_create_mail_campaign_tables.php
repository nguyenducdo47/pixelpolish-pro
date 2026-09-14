<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_templates', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('kind', 32)->default('transactional');
            $table->string('subject');
            $table->longText('body_html');
            $table->text('body_text')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::create('user_mail_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->timestamp('marketing_unsubscribed_at')->nullable();
            $table->string('unsubscribe_token', 64)->unique();
            $table->timestamps();
        });

        Schema::create('mail_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_template_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('kind', 32);
            $table->json('audience')->nullable();
            $table->timestamp('send_at')->nullable();
            $table->string('timezone', 64)->default('Asia/Ho_Chi_Minh');
            $table->string('status', 32)->default('draft');
            $table->unsignedInteger('recipients_total')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->unsignedInteger('skipped_count')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'send_at']);
        });

        Schema::create('mail_campaign_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email');
            $table->string('status', 32)->default('pending');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['mail_campaign_id', 'email']);
            $table->index(['mail_campaign_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_campaign_recipients');
        Schema::dropIfExists('mail_campaigns');
        Schema::dropIfExists('user_mail_preferences');
        Schema::dropIfExists('mail_templates');
    }
};
