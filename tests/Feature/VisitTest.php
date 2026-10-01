<?php

namespace Tests\Feature;

use App\Models\EventProgram;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_visit_management_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $program = EventProgram::factory()->create([
            'title' => 'Hexycleイベントカフェ',
            'capacity' => 30,
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.visits.index', $program));

        $response->assertStatus(200);
        $response->assertSee('入退場管理');
        $response->assertSee('Hexycleイベントカフェ');
    }

    public function test_admin_can_register_visit(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $program = EventProgram::factory()->create([
            'capacity' => 30,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.visits.store', $program), [
                'guest_name' => 'Test Guest',
            ]);

        $response->assertRedirect(
            route('admin.visits.index', $program)
        );

        $this->assertDatabaseHas('visits', [
            'event_program_id' => $program->id,
            'guest_name' => 'Test Guest',
            'status' => 'inside',
        ]);
    }

    public function test_registering_visit_increases_inside_count(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $program = EventProgram::factory()->create([
            'capacity' => 30,
        ]);

        $this->assertSame(
            0,
            $program->visits()
                ->where('status', 'inside')
                ->count()
        );

        $this
            ->actingAs($admin)
            ->post(route('admin.visits.store', $program), [
                'guest_name' => 'Test Guest',
            ]);

        $this->assertSame(
            1,
            $program->visits()
                ->where('status', 'inside')
                ->count()
        );
    }

    public function test_admin_can_exit_visit(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $visit = Visit::factory()->create([
            'status' => 'inside',
            'exited_at' => null,
        ]);

        $response = $this
            ->actingAs($admin)
            ->patch(route('admin.visits.exit', $visit));

        $response->assertRedirect(
            route(
                'admin.visits.index',
                $visit->event_program_id
            )
        );

        $visit->refresh();

        $this->assertSame(
            'exited',
            $visit->status
        );

        $this->assertNotNull(
            $visit->exited_at
        );
    }

    public function test_visit_cannot_be_registered_when_capacity_is_full(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $program = EventProgram::factory()->create([
            'capacity' => 1,
        ]);

        Visit::factory()->create([
            'event_program_id' => $program->id,
            'status' => 'inside',
            'exited_at' => null,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.visits.store', $program), [
                'guest_name' => 'Second Guest',
            ]);

        $response->assertSessionHasErrors('capacity');

        $this->assertDatabaseMissing('visits', [
            'event_program_id' => $program->id,
            'guest_name' => 'Second Guest',
        ]);
    }

    public function test_customer_cannot_access_visit_management(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $program = EventProgram::factory()->create();

        $this
            ->actingAs($customer)
            ->get(route('admin.visits.index', $program))
            ->assertForbidden();

        $this
            ->actingAs($customer)
            ->post(route('admin.visits.store', $program), [
                'guest_name' => 'Test Guest',
            ])
            ->assertForbidden();
    }
}