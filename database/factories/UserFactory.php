<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => self::uniqueUsername(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'is_admin' => false,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    private static function uniqueUsername(): string
    {
        $base = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) fake()->unique()->userName()) ?: 'user';

        return substr($base, 0, 40);
    }

    public function disabled(?string $reason = 'Disabled for testing'): static
    {
        return $this->state(fn (array $attributes) => [
            'is_disabled' => true,
            'lock_reason' => $reason,
            'disabled_at' => now(),
        ]);
    }
}
