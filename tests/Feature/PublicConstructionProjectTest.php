<?php

namespace Tests\Feature;

use App\Models\ConstructionProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicConstructionProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_index_renders_without_auth_and_shows_featured_project(): void
    {
        $project = ConstructionProject::create([
            'name' => 'ສ້າງສາລາ', 'status' => 'ongoing', 'target_amount' => 1000000,
        ]);
        $project->transactions()->create(['type' => 'income', 'amount' => 300000, 'transaction_date' => now()]);

        $response = $this->get('/public/construction-projects');

        $response->assertStatus(200);
        $response->assertSee('ໂຄງການທີ່ກຳລັງລະດົມທຶນ');
        $response->assertSee('ສ້າງສາລາ');
        $response->assertSee('30%');
    }

    public function test_status_filter_works(): void
    {
        ConstructionProject::create(['name' => 'ໂຄງການ ກຳລັງ', 'status' => 'ongoing']);
        ConstructionProject::create(['name' => 'ໂຄງການ ສຳເລັດ', 'status' => 'completed']);

        $response = $this->get('/public/construction-projects?status=completed');
        $response->assertStatus(200);
        $response->assertSee('ໂຄງການ ສຳເລັດ');
        $response->assertDontSee('ໂຄງການ ກຳລັງ');
    }

    public function test_public_show_page_renders_ledger_without_auth(): void
    {
        $project = ConstructionProject::create(['name' => 'ຂຸດສະນ້ຳ', 'status' => 'completed']);
        $project->transactions()->create([
            'type' => 'income', 'amount' => 500000, 'transaction_date' => now(), 'description' => 'ບໍລິຈາກຈາກຄອບຄົວ ສົມສະໄໝ',
        ]);
        $project->transactions()->create([
            'type' => 'expense', 'amount' => 200000, 'transaction_date' => now(), 'description' => 'ຄ່າແຮງງານ',
        ]);

        $response = $this->get(route('construction-projects.public.show', $project));

        $response->assertStatus(200);
        $response->assertSee('ຂຸດສະນ້ຳ');
        $response->assertSee('ບໍລິຈາກຈາກຄອບຄົວ ສົມສະໄໝ');
        $response->assertSee('ຄ່າແຮງງານ');
    }
}
