<?php

namespace Tests\Feature;

use App\Models\CbfolCommittee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CbfolCommitteeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_public_cbfol_committees_page(): void
    {
        CbfolCommittee::create([
            'name' => 'ພຣະອາຈານໃຫຍ່ ມະຫາເຫວດ ມະເສໄນ',
            'title' => 'ປະທານ ສູນກາງ ອພສ',
            'category' => 'president',
            'term' => 'ສະໄໝທີ IX',
            'status' => 'active',
        ]);

        $response = $this->get('/info/cbfol-committees');
        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_admin_cbfol_committees(): void
    {
        $response = $this->get('/cbfol-committees');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_view_admin_cbfol_committees(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/cbfol-committees');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_cbfol_committee_member(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/cbfol-committees', [
            'name' => 'ພຣະອາຈານ ທົດສອບ',
            'title' => 'ກຳມະການ',
            'category' => 'member',
            'term' => 'ສະໄໝທີ IX',
            'term_year' => '2025',
            'status' => 'active',
            'sort_order' => 10,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('cbfol_committees', [
            'name' => 'ພຣະອາຈານ ທົດສອບ',
            'category' => 'member',
        ]);
    }

    public function test_admin_can_update_cbfol_committee_member(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $member = CbfolCommittee::create([
            'name' => 'ພຣະອາຈານ ເລີ່ມຕົ້ນ',
            'title' => 'ກຳມະການ',
            'category' => 'member',
            'term' => 'ສະໄໝທີ IX',
            'status' => 'active',
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($admin)->post("/cbfol-committees/{$member->id}/update", [
            'name' => 'ພຣະອາຈານ ແກ້ໄຂແລ້ວ',
            'title' => 'ຮອງປະທານ',
            'category' => 'president',
            'term' => 'ສະໄໝທີ IX',
            'status' => 'active',
            'sort_order' => 2,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('cbfol_committees', [
            'id' => $member->id,
            'name' => 'ພຣະອາຈານ ແກ້ໄຂແລ້ວ',
            'category' => 'president',
        ]);
    }

    public function test_admin_can_delete_cbfol_committee_member(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $member = CbfolCommittee::create([
            'name' => 'ພຣະອາຈານ ຈະລຶບ',
            'title' => 'ກຳມະການ',
            'category' => 'member',
            'term' => 'ສະໄໝທີ IX',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->delete("/cbfol-committees/{$member->id}");

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('cbfol_committees', [
            'id' => $member->id,
        ]);
    }

    public function test_admin_can_attach_photo_using_google_drive_url(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $member = CbfolCommittee::create([
            'name' => 'ພຣະອາຈານ ທົດສອບຮູບ',
            'category' => 'standing',
            'term' => 'ສະໄໝທີ IX',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post("/cbfol-committees/{$member->id}/update", [
            'name' => 'ພຣະອາຈານ ທົດສອບຮູບ',
            'category' => 'standing',
            'term' => 'ສະໄໝທີ IX',
            'status' => 'active',
            'photo_url_input' => 'https://drive.google.com/file/d/16_kYdyaER_URcErspL0xaCd5mc-2OeET/view?usp=sharing',
        ]);

        $response->assertSessionHas('success');
        $member->refresh();
        $this->assertEquals('https://lh3.googleusercontent.com/d/16_kYdyaER_URcErspL0xaCd5mc-2OeET', $member->photo);
        $this->assertEquals('https://lh3.googleusercontent.com/d/16_kYdyaER_URcErspL0xaCd5mc-2OeET', $member->photo_url);
    }

    public function test_admin_can_upload_photo_file(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->image('monk.jpg', 600, 600);

        $response = $this->actingAs($admin)->post('/cbfol-committees', [
            'name' => 'ພຣະອາຈານ ໄຟລ໌',
            'category' => 'standing',
            'term' => 'ສະໄໝທີ IX',
            'status' => 'active',
            'photo' => $file,
        ]);

        $response->assertSessionHas('success');
        $created = CbfolCommittee::where('name', 'ພຣະອາຈານ ໄຟລ໌')->first();
        $this->assertNotNull($created->photo);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($created->photo);
    }
}
