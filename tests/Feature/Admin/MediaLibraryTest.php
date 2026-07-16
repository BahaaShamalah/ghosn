<?php

namespace Tests\Feature\Admin;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_and_delete_media(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('logo.png', 200, 200);

        $this->actingAs($user)
            ->post(route('admin.media.store'), ['file' => $file])
            ->assertRedirect(route('admin.media.index'));

        $media = Media::query()->first();
        $this->assertNotNull($media);
        $this->assertSame('logo.png', $media->original_filename);
        Storage::disk('public')->assertExists($media->path);

        $this->actingAs($user)
            ->delete(route('admin.media.destroy', $media))
            ->assertRedirect();

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    public function test_guest_cannot_access_media_library(): void
    {
        $this->get(route('admin.media.index'))->assertRedirect(route('admin.login'));
    }
}
