<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use App\Models\Pegawai;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LemburController extends Controller
{
    /**
     * Tampilkan form publik pengajuan lembur
     */
    public function publicCreate()
    {
        if (!Setting::isLemburEnabled()) {
            return redirect()->route('public.pengajuan')->with('info', 'Fitur Simpanan Jam Lembur saat ini sedang dinonaktifkan.');
        }

        $pegawais = Pegawai::with('divisi')->orderBy('nama')->get();
        return view('public.pengajuan_lembur', compact('pegawais'));
    }

    /**
     * Simpan form publik pengajuan lembur (Mendukung Jam & Menit Bebas)
     */
    public function publicStore(Request $request)
    {
        if (!Setting::isLemburEnabled()) {
            return redirect()->route('public.pengajuan')->with('error', 'Fitur Simpanan Jam Lembur saat ini sedang dinonaktifkan.');
        }

        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'tanggal_lembur' => 'required|date',
            'durasi_jam' => 'nullable|integer|min:0|max:24',
            'durasi_menit' => 'nullable|integer|min:0|max:59',
            'kegiatan' => 'required|string|max:500',
            'file_bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'pegawai_id.required' => 'Pilih nama pegawai pemohon lembur.',
            'tanggal_lembur.required' => 'Pilih tanggal pelaksanaan lembur.',
            'kegiatan.required' => 'Jelaskan kegiatan/keperluan lembur.',
            'file_bukti.max' => 'Ukuran file lampiran maksimal 2MB.',
        ]);

        $jam = (int) ($request->durasi_jam ?? 0);
        $menit = (int) ($request->durasi_menit ?? 0);
        $totalMenit = ($jam * 60) + $menit;

        // Validasi: Minimal 1 Menit Lembur
        if ($totalMenit <= 0) {
            return back()->withInput()->with('error', "Tentukan durasi jam atau menit pelaksanaan lembur.");
        }

        $pegawai = Pegawai::findOrFail($request->pegawai_id);

        $fileBukti = null;
        if ($request->hasFile('file_bukti')) {
            $file = $request->file('file_bukti');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/berkas'), $filename);
            $fileBukti = $filename;
        }

        // Generate Kode Tracking Lembur: LBR-YYYYMM-XXXX
        $kodeTracking = 'LBR-' . date('Ym') . '-' . strtoupper(Str::random(5));

        $lembur = Lembur::create([
            'pegawai_id' => $pegawai->id,
            'kode_tracking' => $kodeTracking,
            'tanggal_lembur' => $request->tanggal_lembur,
            'jumlah_jam' => (int) ceil($totalMenit / 60),
            'jumlah_menit' => $totalMenit,
            'kegiatan' => $request->kegiatan,
            'file_bukti' => $fileBukti,
            'status' => 'pending_kadiv',
        ]);

        return redirect()->route('public.pengajuan_lembur')->with('success', "Pengajuan klaim lembur ({$lembur->durasi_formatted}) berhasil dikirim!\nKode Tracking: {$kodeTracking}\nStatus: Menunggu Persetujuan Atasan Langsung.");
    }

    /**
     * Dashboard internal manajemen persetujuan lembur
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Lembur::with(['pegawai.divisi'])->latest();

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Kadiv hanya melihat lembur dari pegawainya
        if ($user->isKadiv() && $user->divisi_id) {
            $query->whereHas('pegawai', function ($q) use ($user) {
                $q->where('divisi_id', $user->divisi_id);
            });
        }

        $lemburs = $query->paginate(15)->withQueryString();

        return view('lembur.index', compact('lemburs'));
    }

    /**
     * Persetujuan Level 1: Kepala Divisi / Kaprodi
     */
    public function approveKadiv(Lembur $lembur)
    {
        $user = Auth::user();
        if (!$user->isKadiv() && !$user->isHrd()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $lembur->update(['status' => 'pending_hrd']);

        return back()->with('success', "Klaim lembur {$lembur->kode_tracking} disetujui Kepala Divisi dan diteruskan ke HRD.");
    }

    /**
     * Persetujuan Level 2 (Final): Tim HRD & Kepegawaian
     */
    public function approveHrd(Lembur $lembur)
    {
        $user = Auth::user();
        if (!$user->isHrd()) {
            abort(403, 'Akses tidak diizinkan. Hanya HRD yang berhak memberikan approval final.');
        }

        $lembur->update(['status' => 'approved']);

        // Tambahkan total menit lembur ke saldo lembur pegawai
        $menitTambah = $lembur->jumlah_menit ?: ($lembur->jumlah_jam * 60);
        $lembur->pegawai->increment('saldo_lembur', $menitTambah);

        return back()->with('success', "Klaim lembur {$lembur->kode_tracking} BERHASIL DISETUJUI FINAL!\n+{$lembur->durasi_formatted} telah ditambahkan ke Saldo Lembur {$lembur->pegawai->nama}.");
    }

    /**
     * Tolak Pengajuan Lembur
     */
    public function reject(Request $request, Lembur $lembur)
    {
        $request->validate([
            'catatan_penolakan' => 'required|string|max:500',
        ]);

        $lembur->update([
            'status' => 'rejected',
            'catatan_penolakan' => $request->catatan_penolakan,
        ]);

        return back()->with('error', "Klaim lembur {$lembur->kode_tracking} telah ditolak.");
    }
}
