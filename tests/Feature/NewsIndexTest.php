<?php

namespace Tests\Feature;

use App\Livewire\News\NewsIndex;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NewsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_publish_and_delete_news(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(NewsIndex::class)
            ->set('title', 'ຫົວຂໍ້ທົດສອບ')
            ->set('content', 'ເນື້ອຫາທົດສອບ')
            ->call('save')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('news', ['title' => 'ຫົວຂໍ້ທົດສອບ', 'status' => 1]);

        $news = News::first();

        Livewire::actingAs($admin)
            ->test(NewsIndex::class)
            ->call('togglePublish', $news->id);

        $this->assertFalse($news->fresh()->status);

        Livewire::actingAs($admin)
            ->test(NewsIndex::class)
            ->call('confirmDelete', $news->id)
            ->call('delete');

        $this->assertDatabaseMissing('news', ['id' => $news->id]);
    }

    public function test_staff_only_sees_published_news_and_cannot_manage(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $published = News::create([
            'title' => 'ຂ່າວເຜີຍແຜ່', 'slug' => 'news-published', 'content' => 'x',
            'status' => 1, 'published_at' => now(),
        ]);
        News::create([
            'title' => 'ຂ່າວຮ່າງ', 'slug' => 'news-draft', 'content' => 'x',
            'status' => 0,
        ]);

        $component = Livewire::actingAs($staff)->test(NewsIndex::class);

        $component->assertSee('ຂ່າວເຜີຍແຜ່')
            ->assertDontSee('ຂ່າວຮ່າງ')
            ->assertDontSee('ເພີ່ມຂ່າວ');

        // Non-admin openEdit is blocked by abort_unless(403): component state stays untouched.
        $component->call('openEdit', $published->id)
            ->assertSet('editId', null)
            ->assertSet('showModal', false);
    }
}
