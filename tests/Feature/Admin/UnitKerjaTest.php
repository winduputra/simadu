<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use App\Models\UnitKerja;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitKerjaTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['slug' => 'super-admin'], ['nama' => 'Super Admin']);
        $this->admin = User::factory()->create([
            'role_id' => $role->id,
        ]);
    }

    public function test_admin_can_list_unit_kerja(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.unit-kerja.index'));
        $response->assertOk();
    }

    public function test_admin_can_create_unit_kerja(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.unit-kerja.store'), [
            'nama' => 'Test Division',
            'kode' => 'TDIV',
            'deskripsi' => 'This is a test division',
        ]);

        $response->assertRedirect(route('admin.unit-kerja.index'));
        $this->assertDatabaseHas('unit_kerjas', [
            'kode' => 'TDIV',
            'nama' => 'Test Division',
        ]);
    }

    public function test_admin_can_delete_unit_kerja(): void
    {
        $unit = UnitKerja::create([
            'nama' => 'To Delete',
            'kode' => 'TDEL',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.unit-kerja.destroy', $unit));

        $response->assertRedirect(route('admin.unit-kerja.index'));
        $this->assertDatabaseMissing('unit_kerjas', ['id' => $unit->id]);
    }

    public function test_admin_cannot_delete_unit_kerja_with_users(): void
    {
        $unit = UnitKerja::create([
            'nama' => 'With Users',
            'kode' => 'WUSR',
        ]);

        User::factory()->create([
            'unit_kerja_id' => $unit->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.unit-kerja.destroy', $unit));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('unit_kerjas', ['id' => $unit->id]);
    }
}
