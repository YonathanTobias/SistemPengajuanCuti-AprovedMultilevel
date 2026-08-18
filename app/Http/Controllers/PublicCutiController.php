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
            'alasan' => 'nullable|string',
            'file_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'pegawai_id.required' => 'Silakan pilih pegawai pengaju cuti.',
            'tanggal_cuti.after_or_equal' => 'Tanggal cuti minimal hari ini.',
            'file_pendukung.max' => 'Ukuran file pendukung maksimal 2MB.',
        ]);

        $pegawai = Pegawai::findOrFail($request->pegawai_id);

        // Single-day leave policy: 1 submission = 1 day
        $jumlahHari = 1;
        $tanggalCuti = $request->tanggal_cuti;

        // Check annual leave quota (1 day)
        if ($request->jenis_cuti === 'Cuti Tahunan') {
            if ($pegawai->sisa_cuti < 1) {
                return back()->withInput()->with('error', "Sisa cuti tahunan saudara {$pegawai->nama} telah habis (0 hari). Pengajuan tidak dapat diproses.");
            }
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
            'alasan' => $request->alasan ?: 'Pengajuan Cuti Tahunan Pegawai',
            'alamat_cuti' => '-',
            'no_hp_cuti' => $pegawai->no_hp ?? '-',
            'file_pendukung' => $filePath,
            'status' => 'pending_kadiv',
        ]);

        return redirect()->route('public.tracking', ['kode' => $kodeTracking])
            ->with('success', "Pengajuan cuti 1 hari untuk tanggal " . Carbon::parse($tanggalCuti)->translatedFormat('d F Y') . " berhasil dikirim! Kode Tracking Anda: {$kodeTracking}");
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
