<?php

namespace Tests\Feature;

use App\Livewire\ConstructionProjects\ProjectIndex;
use App\Models\ConstructionProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ConstructionProjectIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_project_add_transactions_and_see_summary(): void
    {
        $user = User::factory()->create(['role' => 'staff']);

        Livewire::actingAs($user)
            ->test(ProjectIndex::class)
            ->set('name', 'ກໍ່ສ້າງສາລາ')
            ->set('status', 'ongoing')
            ->call('save')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('construction_projects', ['name' => 'ກໍ່ສ້າງສາລາ']);

        $project = ConstructionProject::first();

        $component = Livewire::actingAs($user)
            ->test(ProjectIndex::class)
            ->call('viewProject', $project->id)
            ->assertSet('viewingProjectId', $project->id);

        $component->set('tx_type', 'income')
            ->set('tx_amount', '500000')
            ->set('tx_transaction_date', now()->format('Y-m-d'))
            ->set('tx_description', 'ບໍລິຈາກ')
            ->call('saveTx')
            ->assertSet('showTxModal', false);

        $component->set('tx_type', 'expense')
            ->set('tx_amount', '200000')
            ->set('tx_transaction_date', now()->format('Y-m-d'))
            ->set('tx_description', 'ຄ່າຊີມັງ')
            ->call('saveTx');

        $this->assertDatabaseHas('construction_transactions', ['description' => 'ບໍລິຈາກ', 'type' => 'income']);
        $this->assertDatabaseHas('construction_transactions', ['description' => 'ຄ່າຊີມັງ', 'type' => 'expense']);

        $project->refresh();
        $this->assertEquals(500000, $project->total_income);
        $this->assertEquals(200000, $project->total_expense);
        $this->assertEquals(300000, $project->balance);

        $component->assertSee('ບໍລິຈາກ')->assertSee('ຄ່າຊີມັງ');
    }

    public function test_deleting_project_cascades_transactions(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $project = ConstructionProject::create(['name' => 'ໂຄງການທົດສອບ', 'status' => 'ongoing', 'user_id' => $user->id]);
        $project->transactions()->create([
            'type' => 'expense', 'amount' => 100, 'transaction_date' => now(), 'user_id' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(ProjectIndex::class)
            ->call('confirmDelete', $project->id)
            ->call('delete');

        $this->assertDatabaseMissing('construction_projects', ['id' => $project->id]);
        $this->assertDatabaseMissing('construction_transactions', ['construction_project_id' => $project->id]);
    }
}
