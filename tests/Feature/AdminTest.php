<?php

namespace Tests\Feature;

use App\Models\QrGeneration;
use App\Models\Signer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->staff = User::factory()->create(['role' => 'staff', 'is_active' => true]);
    }

    // --- Signer CRUD ---

    public function test_admin_can_list_signers()
    {
        Signer::factory()->count(3)->create();
        $this->actingAs($this->admin)
            ->get('/admin/signers')
            ->assertOk()
            ->assertSee('Penandatangan');
    }

    public function test_staff_cannot_access_signers()
    {
        $this->actingAs($this->staff)
            ->get('/admin/signers')
            ->assertForbidden();
    }

    public function test_admin_can_create_signer()
    {
        $this->actingAs($this->admin)->post('/admin/signers', [
            'name' => 'dr. Test',
            'position' => 'Dokter Umum',
        ])->assertRedirect();
        $this->assertDatabaseHas('signers', ['name' => 'dr. Test']);
    }

    public function test_admin_can_edit_signer()
    {
        $signer = Signer::factory()->create();
        $this->actingAs($this->admin)->put('/admin/signers/' . $signer->id, [
            'name' => 'dr. Updated',
            'position' => 'Kepala Ruangan',
        ])->assertRedirect();
        $this->assertDatabaseHas('signers', ['name' => 'dr. Updated']);
    }

    public function test_admin_can_deactivate_signer()
    {
        $signer = Signer::factory()->create(['is_active' => true]);
        $this->actingAs($this->admin)->delete('/admin/signers/' . $signer->id)
            ->assertRedirect();
        $this->assertDatabaseHas('signers', ['id' => $signer->id, 'is_active' => false]);
    }

    // --- Audit Log ---

    public function test_admin_can_view_logs()
    {
        Signer::factory()->count(2)->create();
        $this->actingAs($this->admin)
            ->get('/admin/logs')
            ->assertOk()
            ->assertSee('Log Audit');
    }

    public function test_staff_cannot_view_logs()
    {
        $this->actingAs($this->staff)
            ->get('/admin/logs')
            ->assertForbidden();
    }

    public function test_logs_show_generated_data()
    {
        $signer = Signer::factory()->create(['name' => 'dr. Budi']);
        QrGeneration::factory()->create([
            'signer_id' => $signer->id,
            'generated_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)
            ->get('/admin/logs')
            ->assertOk()
            ->assertSee('dr. Budi');
    }

    public function test_logs_filter_by_signer_name()
    {
        $signer = Signer::factory()->create(['name' => 'dr. Unik']);
        QrGeneration::factory()->count(3)->create(['signer_id' => $signer->id]);
        QrGeneration::factory()->create();

        $this->actingAs($this->admin)
            ->get('/admin/logs?signer_name=unik')
            ->assertOk()
            ->assertSee('dr. Unik');
    }
}
