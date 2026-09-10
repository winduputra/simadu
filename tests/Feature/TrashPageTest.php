<?php

namespace Tests\Feature;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrashPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_trash_page_renders_actions_for_a_deleted_folder(): void
    {
        $user = User::factory()->create();
        $folder = Folder::create([
            'user_id' => $user->id,
            'nama' => 'Folder Sampah',
        ]);
        $folder->delete();

        $response = $this->actingAs($user)->get(route('trash.index'));

        $response->assertOk();
        $response->assertSee('aria-label="Restore Folder Sampah"', false);
        $response->assertSee('aria-label="Permanently delete Folder Sampah"', false);
    }
}
