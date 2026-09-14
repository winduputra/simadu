<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Folder;
use App\Models\Role;
use App\Models\Share;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class DriveBulkActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_download_mixed_selection_as_recursive_deduplicated_zip(): void
    {
        Storage::fake('nas');
        $user = User::factory()->create();
        $parent = $this->createFolder($user, 'Project');
        $child = $this->createFolder($user, 'Reports', $parent);
        $nestedDocument = $this->createDocument($user, 'summary.txt', 'project/summary.txt', $child);
        $rootDocument = $this->createDocument($user, 'notes.txt', 'notes.txt');
        Storage::disk('nas')->put($nestedDocument->storage_path, 'summary');
        Storage::disk('nas')->put($rootDocument->storage_path, 'notes');

        $response = $this->actingAs($user)->post(route('documents.bulk-download'), [
            'folder_ids' => [$parent->id, $child->id],
            'document_ids' => [$nestedDocument->id, $rootDocument->id],
        ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/zip');
        $archivePath = tempnam(sys_get_temp_dir(), 'bulk-drive-test-');
        file_put_contents($archivePath, $response->streamedContent());
        $archive = new ZipArchive();
        $this->assertTrue($archive->open($archivePath));
        $entries = [];
        for ($index = 0; $index < $archive->numFiles; $index++) {
            $entries[] = $archive->getNameIndex($index);
        }
        $archive->close();
        unlink($archivePath);

        $fileEntries = array_values(array_filter($entries, fn (string $entry): bool => !str_ends_with($entry, '/')));
        $this->assertSame(['Project/Reports/summary.txt', 'notes.txt'], $fileEntries);
        $this->assertNotContains('Reports/', $entries);
    }

    public function test_owner_can_share_selected_folders_and_documents(): void
    {
        $owner = User::factory()->create();
        $recipient = User::factory()->create();
        $folder = $this->createFolder($owner, 'Shared folder');
        $document = $this->createDocument($owner, 'shared.txt', 'shared.txt');

        $response = $this->actingAs($owner)->post(route('shares.bulk-store'), [
            'folder_ids' => [$folder->id],
            'document_ids' => [$document->id],
            'shared_to_type' => 'user',
            'shared_to_id' => $recipient->id,
            'permission' => 'editor',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertSame(2, Share::count());
        $this->assertDatabaseHas('shares', [
            'shareable_type' => Folder::class,
            'shareable_id' => $folder->id,
            'shared_to_type' => User::class,
            'shared_to_id' => $recipient->id,
            'permission' => 'editor',
        ]);
        $this->assertDatabaseHas('shares', [
            'shareable_type' => Document::class,
            'shareable_id' => $document->id,
            'shared_to_type' => User::class,
            'shared_to_id' => $recipient->id,
            'permission' => 'editor',
        ]);
    }

    public function test_bulk_download_sanitizes_archive_path_segments(): void
    {
        Storage::fake('nas');
        $user = User::factory()->create();
        $folder = $this->createFolder($user, '..');
        $document = $this->createDocument($user, 'safe.txt', 'safe.txt', $folder);
        Storage::disk('nas')->put($document->storage_path, 'safe');

        $response = $this->actingAs($user)->post(route('documents.bulk-download'), [
            'folder_ids' => [$folder->id],
        ]);

        $archivePath = tempnam(sys_get_temp_dir(), 'bulk-drive-test-');
        file_put_contents($archivePath, $response->streamedContent());
        $archive = new ZipArchive();
        $this->assertTrue($archive->open($archivePath));
        $entries = [];
        for ($index = 0; $index < $archive->numFiles; $index++) {
            $entries[] = $archive->getNameIndex($index);
        }
        $archive->close();
        unlink($archivePath);

        $this->assertSame(['item/', 'item/safe.txt'], $entries);
    }

    public function test_bulk_actions_reject_items_owned_by_another_user(): void
    {
        Storage::fake('nas');
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $recipient = User::factory()->create();
        $document = $this->createDocument($otherUser, 'private.txt', 'private.txt');
        Storage::disk('nas')->put($document->storage_path, 'private');

        $this->actingAs($user)->post(route('documents.bulk-download'), [
            'document_ids' => [$document->id],
        ])->assertForbidden();

        $this->actingAs($user)->post(route('shares.bulk-store'), [
            'document_ids' => [$document->id],
            'shared_to_type' => 'user',
            'shared_to_id' => $recipient->id,
            'permission' => 'viewer',
        ])->assertForbidden();
        $this->assertSame(0, Share::count());
    }

    public function test_super_admin_can_run_bulk_actions_for_other_users_items(): void
    {
        Storage::fake('nas');
        $owner = User::factory()->create();
        $superAdminRole = Role::create(['nama' => 'Super Admin', 'slug' => 'super-admin']);
        $superAdmin = User::factory()->create(['role_id' => $superAdminRole->id]);
        $document = $this->createDocument($owner, 'audit.txt', 'audit.txt');
        Storage::disk('nas')->put($document->storage_path, 'audit');

        $this->actingAs($superAdmin)->post(route('documents.bulk-download'), [
            'document_ids' => [$document->id],
        ])->assertOk();
    }

    public function test_bulk_actions_require_at_least_one_valid_selection(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('documents.bulk-download'), [])
            ->assertSessionHasErrors(['document_ids', 'folder_ids']);

        $this->actingAs($user)->post(route('shares.bulk-store'), [
            'shared_to_type' => 'user',
            'shared_to_id' => $user->id,
            'permission' => 'viewer',
        ])->assertSessionHasErrors(['document_ids', 'folder_ids']);
    }

    private function createFolder(User $user, string $name, ?Folder $parent = null): Folder
    {
        return Folder::create([
            'user_id' => $user->id,
            'parent_id' => $parent?->id,
            'nama' => $name,
        ]);
    }

    private function createDocument(
        User $user,
        string $originalName,
        string $storagePath,
        ?Folder $folder = null,
    ): Document {
        return Document::create([
            'folder_id' => $folder?->id,
            'user_id' => $user->id,
            'nama' => pathinfo($originalName, PATHINFO_FILENAME),
            'nama_file_asli' => $originalName,
            'mime_type' => 'text/plain',
            'ukuran' => 5,
            'storage_path' => $storagePath,
        ]);
    }
}
