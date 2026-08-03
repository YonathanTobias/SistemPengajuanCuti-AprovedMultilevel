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

    public function test_public_can_submit_leave_and_track()
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
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+2 days')),
            'alasan' => 'Urusan keluarga penting di luar kota',
            'alamat_cuti' => 'Jl. Raya Langsep No. 45 Malang',
            'no_hp_cuti' => '08123456789',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('cutis', [
            'pegawai_id' => $pegawai->id,
            'status' => 'pending_kadiv',
        ]);
    }

    public function test_multi_level_approval_workflow()
    {
        $divisi = Divisi::create(['kode_divisi' => 'DIV-01', 'nama_divisi' => 'Keperawatan']);
        
        $kadiv = User::create([
            'name' => 'Kaprodi Keperawatan',
            'email' => 'kadiv@stikes.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'kadiv',
            'divisi_id' => $divisi->id,
        ]);

        $hrd = User::create([
            'name' => 'HRD Admin',
            'email' => 'hrd@stikes.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'hrd',
        ]);

        $ketua = User::create([
            'name' => 'Ketua STIKes',
            'email' => 'ketua@stikes.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'ketua',
        ]);

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

        $cuti = Cuti::create([
            'kode_tracking' => 'CUTI-TEST-001',
            'pegawai_id' => $pegawai->id,
            'jenis_cuti' => 'Cuti Tahunan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 day')),
            'jumlah_hari' => 2,
            'alasan' => 'Cuti keperluan keluarga',
            'alamat_cuti' => 'Malang',
            'no_hp_cuti' => '08123456789',
            'status' => 'pending_kadiv',
        ]);

        // 1. Kadiv Approval
        $this->actingAs($kadiv)
            ->post(route('approval.kadiv', $cuti->id), ['catatan' => 'Disetujui Kadiv'])
            ->assertRedirect();
        
        $this->assertEquals('pending_hrd', $cuti->fresh()->status);

        // 2. HRD Approval
        $this->actingAs($hrd)
            ->post(route('approval.hrd', $cuti->id), ['catatan' => 'Disetujui HRD'])
            ->assertRedirect();

        $this->assertEquals('pending_ketua', $cuti->fresh()->status);

        // 3. Ketua STIKes Approval
        $this->actingAs($ketua)
            ->post(route('approval.ketua', $cuti->id), ['catatan' => 'Disetujui Ketua'])
            ->assertRedirect();

        $this->assertEquals('approved', $cuti->fresh()->status);
        $this->assertEquals(10, $pegawai->fresh()->sisa_cuti);
    }

    public function test_automatic_kadiv_user_creation_on_new_divisi()
    {
        $hrd = User::create([
            'name' => 'HRD Admin',
            'email' => 'hrd@stikes.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'hrd',
        ]);

        $response = $this->actingAs($hrd)
            ->post(route('divisi.store'), [
                'kode_divisi' => 'PRODI-D3FARM',
                'nama_divisi' => 'Prodi D3 Farmasi',
                'deskripsi' => 'Program Studi D3 Farmasi STIKes Panti Waluya',
            ]);

        $response->assertRedirect(route('divisi.index'));

        $divisi = Divisi::where('kode_divisi', 'PRODI-D3FARM')->first();
        $this->assertNotNull($divisi);

        // Verify Kadiv Account was automatically created!
        $this->assertDatabaseHas('users', [
            'role' => 'kadiv',
            'divisi_id' => $divisi->id,
            'email' => 'kadiv.prodi-d3-farmasi@stikespantiwaluya.ac.id',
        ]);
    }
}
