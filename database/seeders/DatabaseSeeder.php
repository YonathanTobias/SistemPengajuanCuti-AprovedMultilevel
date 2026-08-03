<?php

namespace Database\Seeders;

use App\Models\Cuti;
use App\Models\Divisi;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Divisi / Prodi
        $divKpr = Divisi::create([
            'kode_divisi' => 'PRODI-S1KPR',
            'nama_divisi' => 'Prodi S1 Keperawatan',
            'deskripsi' => 'Program Studi Sarjana Keperawatan STIKes Panti Waluya Malang',
        ]);

        $divRm = Divisi::create([
            'kode_divisi' => 'PRODI-D3RM',
            'nama_divisi' => 'Prodi D3 Rekam Medis',
            'deskripsi' => 'Program Studi D3 Rekam Medis & Informasi Kesehatan',
        ]);

        $divKeu = Divisi::create([
            'kode_divisi' => 'DIV-KEU',
            'nama_divisi' => 'Divisi Keuangan',
            'deskripsi' => 'Bagian Keuangan dan Administrasi Keuangan',
        ]);

        $divHrd = Divisi::create([
            'kode_divisi' => 'DIV-HRD',
            'nama_divisi' => 'Divisi HRD & Kepegawaian',
            'deskripsi' => 'Bagian Sumber Daya Manusia & Kepegawaian',
        ]);

        // 2. Create Positional / Generic User Accounts (Tanpa Nama Perorangan)
        // HRD Account
        User::create([
            'name' => 'Tim HRD & Kepegawaian',
            'email' => 'hrd@stikespantiwaluya.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'hrd',
            'divisi_id' => $divHrd->id,
        ]);

        // Ketua STIKes Account
        User::create([
            'name' => 'Ketua STIKes Panti Waluya',
            'email' => 'ketua@stikespantiwaluya.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'ketua',
            'divisi_id' => null,
        ]);

        // Kepala Divisi Accounts (Generik Jabatan)
        User::create([
            'name' => 'Kepala Prodi S1 Keperawatan',
            'email' => 'kadiv.keperawatan@stikespantiwaluya.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'kadiv',
            'divisi_id' => $divKpr->id,
        ]);

        User::create([
            'name' => 'Kepala Prodi D3 Rekam Medis',
            'email' => 'kadiv.rekammedis@stikespantiwaluya.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'kadiv',
            'divisi_id' => $divRm->id,
        ]);

        User::create([
            'name' => 'Kepala Divisi Keuangan',
            'email' => 'kadiv.keuangan@stikespantiwaluya.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'kadiv',
            'divisi_id' => $divKeu->id,
        ]);

        // 3. Create Sample Pegawai
        $p1 = Pegawai::create([
            'nip' => '202401001',
            'nama' => 'Ahmad Fauzi, S.Kep., Ns.',
            'divisi_id' => $divKpr->id,
            'jabatan' => 'Dosen S1 Keperawatan',
            'email' => 'ahmad.fauzi@stikespantiwaluya.ac.id',
            'no_hp' => '081234567890',
            'jatah_cuti' => 12,
            'sisa_cuti' => 10,
        ]);

        $p2 = Pegawai::create([
            'nip' => '202401002',
            'nama' => 'Siti Nurhaliza, A.Md.RMIK',
            'divisi_id' => $divRm->id,
            'jabatan' => 'Staff Laboratorium Rekam Medis',
            'email' => 'siti.nurhaliza@stikespantiwaluya.ac.id',
            'no_hp' => '082345678901',
            'jatah_cuti' => 12,
            'sisa_cuti' => 12,
        ]);

        $p3 = Pegawai::create([
            'nip' => '202401003',
            'nama' => 'Dedi Kurniawan, S.E.',
            'divisi_id' => $divKeu->id,
            'jabatan' => 'Staff Administrasi Keuangan',
            'email' => 'dedi.kurniawan@stikespantiwaluya.ac.id',
            'no_hp' => '083456789012',
            'jatah_cuti' => 12,
            'sisa_cuti' => 8,
        ]);

        // 4. Create Sample Cuti Submissions
        Cuti::create([
            'kode_tracking' => 'CUTI-20260803-A1B2',
            'pegawai_id' => $p1->id,
            'jenis_cuti' => 'Cuti Tahunan',
            'tanggal_mulai' => now()->addDays(2),
            'tanggal_selesai' => now()->addDays(4),
            'jumlah_hari' => 3,
            'alasan' => 'Menghadiri acara keluarga di Surabaya',
            'alamat_cuti' => 'Jl. Gubeng Kertajaya No. 12, Surabaya',
            'no_hp_cuti' => '081234567890',
            'status' => 'pending_kadiv',
        ]);

        Cuti::create([
            'kode_tracking' => 'CUTI-20260803-C3D4',
            'pegawai_id' => $p3->id,
            'jenis_cuti' => 'Cuti Sakit',
            'tanggal_mulai' => now()->subDays(1),
            'tanggal_selesai' => now()->addDays(1),
            'jumlah_hari' => 3,
            'alasan' => 'Istirahat menderita flu dan demam tinggi sesuai anjuran dokter',
            'alamat_cuti' => 'Jl. Langsep Malang',
            'no_hp_cuti' => '083456789012',
            'status' => 'pending_hrd',
            'catatan_kadiv' => 'Disetujui oleh Kepala Keuangan, berkas pendukung terlampir.',
            'kadiv_approved_at' => now()->subHours(2),
        ]);

        Cuti::create([
            'kode_tracking' => 'CUTI-20260803-E5F6',
            'pegawai_id' => $p2->id,
            'jenis_cuti' => 'Cuti Alasan Penting',
            'tanggal_mulai' => now()->subDays(5),
            'tanggal_selesai' => now()->subDays(3),
            'jumlah_hari' => 3,
            'alasan' => 'Mendampingi orang tua operasi di RS Panti Waluya Sawahan Malang',
            'alamat_cuti' => 'Jl. Nusakambangan Malang',
            'no_hp_cuti' => '082345678901',
            'status' => 'approved',
            'catatan_kadiv' => 'Disetujui Kaprodi RM',
            'kadiv_approved_at' => now()->subDays(6),
            'catatan_hrd' => 'Persyaratan lengkap dan sisa kuota aman',
            'hrd_approved_at' => now()->subDays(5),
            'catatan_ketua' => 'Disetujui sepenuhnya oleh Ketua STIKes',
            'ketua_approved_at' => now()->subDays(5),
        ]);
    }
}
