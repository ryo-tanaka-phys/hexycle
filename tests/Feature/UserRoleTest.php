<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_is_not_admin(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->assertFalse($user->isAdmin());
    }

    public function test_admin_is_admin(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->assertTrue($user->isAdmin());
    }
}
