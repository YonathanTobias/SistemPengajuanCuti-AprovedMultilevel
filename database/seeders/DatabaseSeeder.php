<?php

namespace Database\Seeders;

use App\Models\Cuti;
use App\Models\Divisi;
use App\Models\Pegawai;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Administrative Users
        $hrdDivisi = Divisi::firstOrCreate([
            'kode_divisi' => 'DIV-HRD',
        ], [
            'nama_divisi' => 'Divisi HRD & Kepegawaian',
            'deskripsi' => 'Bagian SDM & Kepegawaian STIKes Panti Waluya Malang',
        ]);

        User::firstOrCreate(['email' => 'hrd@stikespantiwaluya.ac.id'], [
            'name' => 'Tim HRD & Kepegawaian',
            'password' => Hash::make('password123'),
            'role' => 'hrd',
            'divisi_id' => $hrdDivisi->id,
        ]);

        User::firstOrCreate(['email' => 'ketua@stikespantiwaluya.ac.id'], [
            'name' => 'Ketua STIKes Panti Waluya',
            'password' => Hash::make('password123'),
            'role' => 'ketua',
            'divisi_id' => null,
        ]);

        // 2. Read and Parse data_cuti.csv
        $csvFile = __DIR__ . '/data_cuti.csv';
        if (!file_exists($csvFile)) {
            return;
        }

        $file = fopen($csvFile, 'r');
        $header = fgetcsv($file, 1000, ';'); // Skip header row

        $divisiCache = [];
        $pegawaiCache = [];
        $cutiCountPerPegawai = [];

        while (($row = fgetcsv($file, 1000, ';')) !== false) {
            if (count($row) < 5) {
                continue;
            }

            $no = trim($row[0]);
            $nip = trim($row[1]);
            $nama = trim($row[2]);
            $namaDivisi = trim($row[3]);
            $tglRaw = trim($row[4]);

            if (empty($nama) || empty($namaDivisi)) {
                continue;
            }

            // Parse Date
            $tglFormatted = null;
            if (str_contains($tglRaw, '/')) {
                // DD/MM/YYYY
                try {
                    $tglFormatted = Carbon::createFromFormat('d/m/Y', $tglRaw)->format('Y-m-d');
                } catch (\Exception $e) {
                    $tglFormatted = date('Y-m-d');
                }
            } elseif (str_starts_with($tglRaw, '0025-')) {
                $tglFormatted = str_replace('0025-', '2025-', $tglRaw);
            } else {
                $tglFormatted = $tglRaw;
            }

            // 1. Get or Create Divisi
            if (!isset($divisiCache[$namaDivisi])) {
                $kodeDiv = 'DIV-' . strtoupper(Str::slug($namaDivisi));
                $divisi = Divisi::firstOrCreate([
                    'nama_divisi' => $namaDivisi,
                ], [
                    'kode_divisi' => substr($kodeDiv, 0, 20),
                    'deskripsi' => 'Unit / Divisi ' . $namaDivisi,
                ]);

                // Create Auto Kadiv User
                $slug = Str::slug($namaDivisi);
                $kadivEmail = "kadiv.{$slug}@stikespantiwaluya.ac.id";
                if (User::where('email', $kadivEmail)->exists()) {
                    $kadivEmail = "kadiv.{$slug}." . Str::lower(Str::random(3)) . "@stikespantiwaluya.ac.id";
                }

                User::firstOrCreate([
                    'role' => 'kadiv',
                    'divisi_id' => $divisi->id,
                ], [
                    'name' => 'Kepala ' . $namaDivisi,
                    'email' => $kadivEmail,
                    'password' => Hash::make('password123'),
                ]);

                $divisiCache[$namaDivisi] = $divisi;
            } else {
                $divisi = $divisiCache[$namaDivisi];
            }

            // 2. Get or Create Pegawai
            $pegawaiKey = $nip !== '-' ? $nip : Str::slug($nama);
            if (!isset($pegawaiCache[$pegawaiKey])) {
                $emailPegawai = Str::slug($nama) . '@stikespantiwaluya.ac.id';
                $pegawai = Pegawai::firstOrCreate([
                    'nama' => $nama,
                    'divisi_id' => $divisi->id,
                ], [
                    'nip' => $nip !== '-' ? $nip : 'NIP-' . rand(1000, 9999),
                    'jabatan' => $namaDivisi,
                    'email' => $emailPegawai,
                    'no_hp' => '08' . rand(100000000, 999999999),
                    'jatah_cuti' => 12,
                    'sisa_cuti' => 12,
                ]);
                $pegawaiCache[$pegawaiKey] = $pegawai;
            } else {
                $pegawai = $pegawaiCache[$pegawaiKey];
            }

            // Track count per employee
            if (!isset($cutiCountPerPegawai[$pegawai->id])) {
                $cutiCountPerPegawai[$pegawai->id] = 0;
            }
            $cutiCountPerPegawai[$pegawai->id]++;

            // 3. Create Cuti Record (1 Day per Submission)
            $kodeTracking = 'CUTI-' . date('Ymd', strtotime($tglFormatted)) . '-' . sprintf('%04d', $no);

            Cuti::create([
                'kode_tracking' => $kodeTracking,
                'pegawai_id' => $pegawai->id,
                'jenis_cuti' => 'Cuti Tahunan',
                'tanggal_mulai' => $tglFormatted,
                'tanggal_selesai' => $tglFormatted,
                'jumlah_hari' => 1,
                'alasan' => 'Pengajuan Cuti Tahunan Pegawai',
                'alamat_cuti' => 'Malang',
                'no_hp_cuti' => $pegawai->no_hp,
                'status' => 'approved',
                'catatan_kadiv' => 'Disetujui oleh Kepala Divisi / Kaprodi',
                'kadiv_approved_at' => Carbon::parse($tglFormatted)->subDays(2),
                'catatan_hrd' => 'Disetujui oleh Tim HRD',
                'hrd_approved_at' => Carbon::parse($tglFormatted)->subDays(1),
                'catatan_ketua' => 'Disetujui oleh Ketua STIKes Panti Waluya',
                'ketua_approved_at' => Carbon::parse($tglFormatted)->subDays(1),
            ]);
        }

        fclose($file);

        // Update sisa_cuti per employee
        foreach ($pegawaiCache as $pegawai) {
            $taken = $cutiCountPerPegawai[$pegawai->id] ?? 0;
            $sisa = max(0, 12 - $taken);
            $pegawai->update(['sisa_cuti' => $sisa]);
        }
    }
}
