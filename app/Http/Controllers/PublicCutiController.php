<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\Divisi;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicCutiController extends Controller
{
    public function index()
    {
        $pegawais = Pegawai::with('divisi')->orderBy('nama', 'asc')->get();
        $divisis = Divisi::orderBy('nama_divisi', 'asc')->get();

        return view('public.pengajuan', compact('pegawais', 'divisis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'jenis_cuti' => 'required|string',
            'tanggal_cuti' => 'required|date|after_or_equal:today',
            'izin_jam' => 'nullable|integer|min:0|max:3',
            'izin_menit' => 'nullable|integer|min:0|max:59',
            'alasan' => 'nullable|string',
            'file_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'pegawai_id.required' => 'Silakan pilih pegawai pengaju cuti/izin.',
            'tanggal_cuti.after_or_equal' => 'Tanggal pelaksanaan minimal hari ini.',
            'file_pendukung.max' => 'Ukuran file pendukung maksimal 2MB.',
        ]);

        $pegawai = Pegawai::findOrFail($request->pegawai_id);
        $tanggalCuti = $request->tanggal_cuti;
        $jumlahHari = 1;
        $jumlahJam = null;
        $jumlahMenit = null;

        // 1. Cuti Tahunan (Potong 1 Hari Kuota)
        if ($request->jenis_cuti === 'Cuti Tahunan') {
            if ($pegawai->sisa_cuti < 1) {
                return back()->withInput()->with('error', "Sisa cuti tahunan saudara {$pegawai->nama} telah habis (0 hari). Pengajuan tidak dapat diproses.");
            }
            $jumlahHari = 1;
        }

        // 2. Cuti Kompensasi Lembur Penuh (Tukar 9 Jam = 540 Menit = 1 Hari Libur)
        elseif ($request->jenis_cuti === 'Cuti Kompensasi Lembur') {
            if ($pegawai->saldo_lembur < 540) {
                return back()->withInput()->with('error', "Saldo jam lembur saudara {$pegawai->nama} tidak mencukupi (Minimal 9 Jam / 540 Menit untuk 1 hari libur kompensasi, saldo saat ini: {$pegawai->saldo_lembur_formatted}).");
            }
            $jumlahHari = 1;
            $jumlahJam = 9;
            $jumlahMenit = 540;
        }

        // 3. Izin Parsial Jam Lembur: Pulang Cepat atau Datang Terlambat (Bebas Jam & Menit, Maksimal 3 Jam / 180 Menit)
        elseif (in_array($request->jenis_cuti, ['Izin Pulang Cepat', 'Izin Datang Terlambat'])) {
            $jam = (int) ($request->izin_jam ?? 0);
            $menit = (int) ($request->izin_menit ?? 0);
            $totalMenitIzin = ($jam * 60) + $menit;

            if ($totalMenitIzin <= 0) {
                return back()->withInput()->with('error', "Silakan tentukan durasi jam atau menit izin yang dibutuhkan.");
            }

            if ($totalMenitIzin > 180) {
                return back()->withInput()->with('error', "Izin pulang cepat / datang terlambat maksimal 3 jam (180 menit) per pengajuan.");
            }

            if ($pegawai->saldo_lembur < $totalMenitIzin) {
                $formatDibutuhkan = $jam > 0 && $menit > 0 ? "{$jam} Jam {$menit} Menit" : ($jam > 0 ? "{$jam} Jam" : "{$menit} Menit");
                return back()->withInput()->with('error', "Saldo jam lembur saudara {$pegawai->nama} tidak mencukupi (Membutuhkan {$formatDibutuhkan}, saldo Anda saat ini: {$pegawai->saldo_lembur_formatted}).");
            }

            $jumlahHari = 0; // Izin jam tidak memotong 1 hari penuh
            $jumlahJam = (int) ceil($totalMenitIzin / 60);
            $jumlahMenit = $totalMenitIzin;
        }

        $filePath = null;
        if ($request->hasFile('file_pendukung')) {
            $file = $request->file('file_pendukung');
            $filename = time() . '_' . Str::slug($pegawai->nama) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/berkas'), $filename);
            $filePath = 'uploads/berkas/' . $filename;
        }

        // Generate unique tracking code
        $kodeTracking = 'CUTI-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $cuti = Cuti::create([
            'kode_tracking' => $kodeTracking,
            'pegawai_id' => $pegawai->id,
            'jenis_cuti' => $request->jenis_cuti,
            'tanggal_mulai' => $tanggalCuti,
            'tanggal_selesai' => $tanggalCuti,
            'jumlah_hari' => $jumlahHari,
            'jumlah_jam' => $jumlahJam,
            'jumlah_menit' => $jumlahMenit,
            'alasan' => $request->alasan ?: 'Pengajuan Izin/Cuti Pegawai',
            'alamat_cuti' => '-',
            'no_hp_cuti' => $pegawai->no_hp ?? '-',
            'file_pendukung' => $filePath,
            'status' => 'pending_kadiv',
        ]);

        return redirect()->route('public.tracking', ['kode' => $kodeTracking])
            ->with('success', "Pengajuan {$request->jenis_cuti} ({$cuti->durasi_formatted}) untuk tanggal " . Carbon::parse($tanggalCuti)->translatedFormat('d F Y') . " berhasil dikirim! Kode Tracking Anda: {$kodeTracking}");
    }

    public function tracking(Request $request)
    {
        $search = $request->input('kode');
        $cuti = null;
        $cutiList = collect();

        if ($search) {
            $searchClean = trim($search);
            $cuti = Cuti::with(['pegawai.divisi'])
                ->where('kode_tracking', $searchClean)
                ->first();

            if (!$cuti) {
                $cutiList = Cuti::with(['pegawai.divisi'])
                    ->whereHas('pegawai', function ($q) use ($searchClean) {
                        $q->where('nip', $searchClean)
                          ->orWhere('nama', 'like', "%{$searchClean}%");
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        return view('public.tracking', compact('cuti', 'cutiList', 'search'));
    }

    public function suratCuti($kode_tracking)
    {
        $cuti = Cuti::with(['pegawai.divisi'])
            ->where('kode_tracking', $kode_tracking)
            ->where('status', 'approved')
            ->firstOrFail();

        return view('public.surat', compact('cuti'));
    }
}
