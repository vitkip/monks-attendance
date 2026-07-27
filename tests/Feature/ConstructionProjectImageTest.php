<?php

namespace Tests\Feature;

use App\Livewire\ConstructionProjects\ProjectIndex;
use App\Models\ConstructionProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ConstructionProjectImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_upload_project_image_on_create(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'staff']);

        Livewire::actingAs($user)
            ->test(ProjectIndex::class)
            ->set('name', 'ສ້າງສາລາ')
            ->set('image', UploadedFile::fake()->image('project.jpg'))
            ->call('save')
            ->assertSet('showModal', false);

        $project = ConstructionProject::firstWhere('name', 'ສ້າງສາລາ');
        $this->assertNotNull($project->image);
        Storage::disk('public')->assertExists($project->image);
    }

    public function test_replacing_image_deletes_old_file(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'staff']);
        $oldPath = UploadedFile::fake()->image('old.jpg')->store('construction-projects', 'public');
        $project = ConstructionProject::create(['name' => 'ໂຄງການ', 'status' => 'ongoing', 'image' => $oldPath]);

        Livewire::actingAs($user)
            ->test(ProjectIndex::class)
            ->call('openEdit', $project->id)
            ->set('image', UploadedFile::fake()->image('new.jpg'))
            ->call('save');

        Storage::disk('public')->assertMissing($oldPath);
        $this->assertNotEquals($oldPath, $project->fresh()->image);
    }

    public function test_deleting_project_removes_image_file(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'admin']);
        $path = UploadedFile::fake()->image('p.jpg')->store('construction-projects', 'public');
        $project = ConstructionProject::create(['name' => 'ໂຄງການ B', 'status' => 'ongoing', 'image' => $path]);

        Livewire::actingAs($user)
            ->test(ProjectIndex::class)
            ->call('confirmDelete', $project->id)
            ->call('delete');

        Storage::disk('public')->assertMissing($path);
        $this->assertDatabaseMissing('construction_projects', ['id' => $project->id]);
    }

    public function test_public_pages_display_project_image(): void
    {
        Storage::fake('public');
        $path = UploadedFile::fake()->image('sala.jpg')->store('construction-projects', 'public');
        $project = ConstructionProject::create(['name' => 'ສາລາໃໝ່', 'status' => 'ongoing', 'image' => $path]);

        $index = $this->get(route('construction-projects.public.index'));
        $index->assertStatus(200);
        $index->assertSee(Storage::disk('public')->url($path), false);

        $show = $this->get(route('construction-projects.public.show', $project));
        $show->assertStatus(200);
        $show->assertSee(Storage::disk('public')->url($path), false);
    }
}
