<?php

namespace Tests\Feature;

use App\Models\Signer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrGenerationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Signer $signer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->signer = Signer::factory()->create(['is_active' => true]);
    }

    public function test_guest_redirected_to_login()
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_create_form_shows_active_signers()
    {
        $inactive = Signer::factory()->create(['is_active' => false]);
        $this->actingAs($this->admin)
            ->get('/')
            ->assertOk()
            ->assertSee($this->signer->name)
            ->assertDontSee($inactive->name);
    }

    public function test_store_generates_qr_and_returns_png()
    {
        $response = $this->actingAs($this->admin)->post('/qr/generate', [
            'signer_id' => $this->signer->id,
        ]);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/png');
        $this->assertStringStartsWith('attachment; filename="qr-ttd-', $response->headers->get('Content-Disposition'));

        $this->assertDatabaseHas('qr_generations', [
            'signer_id' => $this->signer->id,
            'generated_by' => $this->admin->id,
        ]);
    }

    public function test_store_requires_signer()
    {
        $this->actingAs($this->admin)
            ->post('/qr/generate', [])
            ->assertSessionHasErrors('signer_id');
    }

    public function test_store_rejects_inactive_signer()
    {
        $inactive = Signer::factory()->create(['is_active' => false]);
        $this->actingAs($this->admin)
            ->post('/qr/generate', ['signer_id' => $inactive->id])
            ->assertSessionHasErrors('signer_id');
    }

    public function test_store_saves_letter_number()
    {
        $this->actingAs($this->admin)->post('/qr/generate', [
            'signer_id' => $this->signer->id,
            'letter_number' => '001/RS/VII/2026',
        ]);

        $this->assertDatabaseHas('qr_generations', [
            'signer_id' => $this->signer->id,
            'letter_number' => '001/RS/VII/2026',
        ]);
    }

    public function test_store_without_auth_redirects()
    {
        $this->post('/qr/generate', ['signer_id' => $this->signer->id])
            ->assertRedirect('/login');
    }

    public function test_multiple_generations_have_unique_uuids()
    {
        $uuids = [];
        for ($i = 0; $i < 3; $i++) {
            $response = $this->actingAs($this->admin)->post('/qr/generate', [
                'signer_id' => $this->signer->id,
            ]);
            $response->assertOk();
        }

        $this->assertEquals(3, \App\Models\QrGeneration::count());

        $records = \App\Models\QrGeneration::all();
        $this->assertEquals(3, $records->pluck('uuid')->unique()->count());
    }
}
