<?php

namespace Tests\Feature;

use App\Enums\AccountAuditAction;
use App\Models\AccountAuditLog;
use App\Models\User;
use App\Services\UserAccountService;
use Database\Seeders\LocaleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LocaleSeeder::class);
    }

    public function test_disabled_users_cannot_login(): void
    {
        $user = User::factory()->create([
            'is_disabled' => true,
            'lock_reason' => 'Policy violation',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_registration_writes_audit_log(): void
    {
        $this->post('/register', [
            'name' => 'Audit User',
            'username' => 'audituser',
            'email' => 'audit@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect('/studio/setup');

        $user = User::query()->where('email', 'audit@example.com')->first();

        $this->assertNotNull($user);
        $this->assertDatabaseHas('account_audit_logs', [
            'subject_user_id' => $user->id,
            'action' => AccountAuditAction::Registered->value,
        ]);
    }

    public function test_soft_deleted_users_are_excluded_from_login(): void
    {
        $user = User::factory()->create();
        $user->delete();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');
    }

    public function test_restored_user_can_login_and_clears_disabled_state(): void
    {
        $user = User::factory()->create();
        $user->delete();

        $admin = User::factory()->create(['is_admin' => true]);

        app(UserAccountService::class)->restore(
            User::withTrashed()->findOrFail($user->id),
            $admin,
            'Approved reinstatement',
        );

        $this->assertDatabaseHas('account_audit_logs', [
            'subject_user_id' => $user->id,
            'action' => AccountAuditAction::Restored->value,
            'reason' => 'Approved reinstatement',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/studio/setup');

        $user->refresh();
        $this->assertFalse($user->isDisabled());
        $this->assertNull($user->deleted_at);
    }

    public function test_active_session_is_terminated_when_user_is_disabled(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertRedirect('/studio/setup');

        $user->update(['is_disabled' => true, 'lock_reason' => 'Test']);

        $this->get('/studio/setup')->assertRedirect('/login');
    }

    public function test_audit_log_relationships(): void
    {
        $subject = User::factory()->create();
        $actor = User::factory()->create(['is_admin' => true]);

        $log = AccountAuditLog::query()->create([
            'subject_user_id' => $subject->id,
            'actor_user_id' => $actor->id,
            'action' => AccountAuditAction::Disabled,
            'reason' => 'Test',
        ]);

        $this->assertTrue($log->subject->is($subject));
        $this->assertTrue($log->actor->is($actor));
    }
}
