<?php

namespace Tests\Feature;

use App\Models\Cuti;
use App\Models\Divisi;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SistemCutiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_submit_single_day_leave_and_track()
    {
        $divisi = Divisi::create(['kode_divisi' => 'DIV-01', 'nama_divisi' => 'Keperawatan']);
        $pegawai = Pegawai::create([
            'nip' => '12345',
            'nama' => 'Budi Santoso',
            'divisi_id' => $divisi->id,
            'jabatan' => 'Dosen',
            'email' => 'budi@stikes.ac.id',
            'no_hp' => '08123456789',
            'jatah_cuti' => 12,
            'sisa_cuti' => 12,
        ]);

        $response = $this->post(route('public.pengajuan.store'), [
            'pegawai_id' => $pegawai->id,
            'jenis_cuti' => 'Cuti Tahunan',
            'tanggal_cuti' => date('Y-m-d'),
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('cutis', [
            'pegawai_id' => $pegawai->id,
            'jumlah_hari' => 1,
            'status' => 'pending_kadiv',
        ]);
    }

    public function test_hrd_can_manage_user_accounts()
    {
        $hrd = User::create([
            'name' => 'HRD Admin',
            'email' => 'hrd@stikes.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'hrd',
        ]);

        $divisi = Divisi::create(['kode_divisi' => 'DIV-01', 'nama_divisi' => 'Keperawatan']);

        // HRD can create a new user account
        $response = $this->actingAs($hrd)->post(route('users.store'), [
            'name' => 'Kepala Prodi D3 Keperawatan',
            'email' => 'kadiv.d3keperawatan@stikes.ac.id',
            'password' => 'password123',
            'role' => 'kadiv',
            'divisi_id' => $divisi->id,
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'kadiv.d3keperawatan@stikes.ac.id',
            'role' => 'kadiv',
        ]);

        // HRD can reset password
        $user = User::where('email', 'kadiv.d3keperawatan@stikes.ac.id')->first();
        $this->actingAs($hrd)->post(route('users.reset-password', $user->id), [
            'new_password' => 'newpass123',
        ])->assertRedirect(route('users.index'));
    }

    public function test_kadiv_and_ketua_cannot_access_export_or_crud_or_users()
    {
        $divisi = Divisi::create(['kode_divisi' => 'DIV-01', 'nama_divisi' => 'Keperawatan']);
        
        $kadiv = User::create([
            'name' => 'Kaprodi Keperawatan',
            'email' => 'kadiv@stikes.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'kadiv',
            'divisi_id' => $divisi->id,
        ]);

        $ketua = User::create([
            'name' => 'Ketua STIKes',
            'email' => 'ketua@stikes.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'ketua',
        ]);

        // Kadiv restricted from Export, Pegawai, Divisi, Users
        $this->actingAs($kadiv)->get(route('reports.index'))->assertStatus(403);
        $this->actingAs($kadiv)->get(route('pegawai.index'))->assertStatus(403);
        $this->actingAs($kadiv)->get(route('divisi.index'))->assertStatus(403);
        $this->actingAs($kadiv)->get(route('users.index'))->assertStatus(403);

        // Ketua restricted from Export, Pegawai, Divisi, Users
        $this->actingAs($ketua)->get(route('reports.index'))->assertStatus(403);
        $this->actingAs($ketua)->get(route('pegawai.index'))->assertStatus(403);
        $this->actingAs($ketua)->get(route('divisi.index'))->assertStatus(403);
        $this->actingAs($ketua)->get(route('users.index'))->assertStatus(403);
    }
}
