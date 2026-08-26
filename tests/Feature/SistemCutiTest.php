<?php

namespace Tests\Feature;

use App\Models\Cuti;
use App\Models\Divisi;
use App\Models\Lembur;
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
            'saldo_lembur' => 0,
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

    public function test_overtime_bank_flow_and_compensatory_leave_and_hourly_permissions()
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
            'saldo_lembur' => 0,
        ]);

        $kadiv = User::create([
            'name' => 'Kadiv Keperawatan',
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

        // 1. Submit Overtime 1: 1 Jam 25 Menit (85 Menit)
        $this->post(route('public.pengajuan_lembur.store'), [
            'pegawai_id' => $pegawai->id,
            'tanggal_lembur' => date('Y-m-d'),
            'durasi_jam' => 1,
            'durasi_menit' => 25,
            'kegiatan' => 'Panitia Akreditasi Prodi',
        ])->assertRedirect();

        $lembur1 = Lembur::first();
        $this->assertEquals(85, $lembur1->jumlah_menit);
        $this->assertEquals('1 Jam 25 Menit', $lembur1->durasi_formatted);

        // HRD Approves Overtime 1
        $this->actingAs($hrd)->post(route('lembur.approve-hrd', $lembur1->id));
        $pegawai->refresh();
        $this->assertEquals(85, $pegawai->saldo_lembur);

        // Submit Overtime 2: 10 Jam 0 Menit (600 Menit)
        $this->post(route('public.pengajuan_lembur.store'), [
            'pegawai_id' => $pegawai->id,
            'tanggal_lembur' => date('Y-m-d'),
            'durasi_jam' => 10,
            'durasi_menit' => 0,
            'kegiatan' => 'Panitia Wisuda',
        ])->assertRedirect();

        $lembur2 = Lembur::latest('id')->first();
        $this->actingAs($hrd)->post(route('lembur.approve-hrd', $lembur2->id));
        $pegawai->refresh();
        $this->assertEquals(685, $pegawai->saldo_lembur); // 85 + 600 = 685 Menit (11 Jam 25 Menit)
        $this->assertEquals('11 Jam 25 Menit', $pegawai->saldo_lembur_formatted);

        // 2. Employee Submits Izin Pulang Cepat (1 Jam 30 Menit = 90 Menit)
        $this->post(route('public.pengajuan.store'), [
            'pegawai_id' => $pegawai->id,
            'jenis_cuti' => 'Izin Pulang Cepat',
            'tanggal_cuti' => date('Y-m-d'),
            'izin_jam' => 1,
            'izin_menit' => 30,
        ])->assertRedirect();

        $izin = Cuti::where('jenis_cuti', 'Izin Pulang Cepat')->first();
        $this->assertEquals(90, $izin->jumlah_menit);
        $this->assertEquals('1 Jam 30 Menit', $izin->durasi_formatted);

        // Multi-level approve Izin Pulang Cepat (90 Menit)
        $this->actingAs($kadiv)->post(route('approval.kadiv', $izin->id));
        $this->actingAs($hrd)->post(route('approval.hrd', $izin->id));
        $this->actingAs($ketua)->post(route('approval.ketua', $izin->id));

        $pegawai->refresh();
        $this->assertEquals(595, $pegawai->saldo_lembur); // 685 - 90 = 595 Menit (9 Jam 55 Menit)
        $this->assertEquals('9 Jam 55 Menit', $pegawai->saldo_lembur_formatted);

        // 3. Employee Submits Cuti Kompensasi Lembur (1 Hari = 9 Jam = 540 Menit)
        $this->post(route('public.pengajuan.store'), [
            'pegawai_id' => $pegawai->id,
            'jenis_cuti' => 'Cuti Kompensasi Lembur',
            'tanggal_cuti' => date('Y-m-d'),
        ])->assertRedirect();

        $cuti = Cuti::where('jenis_cuti', 'Cuti Kompensasi Lembur')->first();
        $this->actingAs($kadiv)->post(route('approval.kadiv', $cuti->id));
        $this->actingAs($hrd)->post(route('approval.hrd', $cuti->id));
        $this->actingAs($ketua)->post(route('approval.ketua', $cuti->id));

        $pegawai->refresh();
        $this->assertEquals(55, $pegawai->saldo_lembur); // 595 - 540 = 55 Menit tersisa
        $this->assertEquals('55 Menit', $pegawai->saldo_lembur_formatted);
        $this->assertEquals(12, $pegawai->sisa_cuti);   // Cuti tahunan tetap 12 hari (utuh)
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
    }
}
