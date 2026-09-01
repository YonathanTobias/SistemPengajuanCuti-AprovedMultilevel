<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\Divisi;
use App\Models\Pegawai;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Current active year for Dashboard
        $currentYear = date('Y');

        // Check if there are cuti records for current calendar year, if none default to most recent year with data
        if (!Cuti::forYear($currentYear)->exists()) {
            $latestYear = Cuti::selectRaw('DISTINCT COALESCE(tahun_cuti, CAST(strftime("%Y", tanggal_mulai) AS INTEGER)) as year')
                ->orderBy('year', 'desc')
                ->value('year');
            $currentYear = $latestYear ?: date('Y');
        }

        $query = Cuti::with(['pegawai.divisi'])
            ->forYear($currentYear)
            ->orderBy('created_at', 'desc');

        // Role-based filtering
        if ($user->isKadiv()) {
            if ($user->divisi_id) {
                $query->whereHas('pegawai', function ($q) use ($user) {
                    $q->where('divisi_id', $user->divisi_id);
                });
            }
        }

        // Status filter optional
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $cutis = $query->paginate(10)->withQueryString();

        // Statistics for Current Active Year
        if ($user->isKadiv()) {
            $divisiId = $user->divisi_id;
            $stats = [
                'pending' => Cuti::forYear($currentYear)
                                ->whereHas('pegawai', fn($q) => $q->where('divisi_id', $divisiId))
                                ->where('status', 'pending_kadiv')->count(),
                'approved' => Cuti::forYear($currentYear)
                                ->whereHas('pegawai', fn($q) => $q->where('divisi_id', $divisiId))
                                ->where('status', 'approved')->count(),
                'rejected' => Cuti::forYear($currentYear)
                                ->whereHas('pegawai', fn($q) => $q->where('divisi_id', $divisiId))
                                ->where('status', 'rejected')->count(),
                'total_pegawai' => Pegawai::where('divisi_id', $divisiId)->count(),
            ];
        } elseif ($user->isHrd()) {
            $stats = [
                'pending_kadiv' => Cuti::forYear($currentYear)->where('status', 'pending_kadiv')->count(),
                'pending_hrd' => Cuti::forYear($currentYear)->where('status', 'pending_hrd')->count(),
                'pending_ketua' => Cuti::forYear($currentYear)->where('status', 'pending_ketua')->count(),
                'approved' => Cuti::forYear($currentYear)->where('status', 'approved')->count(),
                'rejected' => Cuti::forYear($currentYear)->where('status', 'rejected')->count(),
                'total_pegawai' => Pegawai::count(),
                'total_divisi' => Divisi::count(),
            ];
        } else { // Ketua STIKes
            $stats = [
                'pending_ketua' => Cuti::forYear($currentYear)->where('status', 'pending_ketua')->count(),
                'approved' => Cuti::forYear($currentYear)->where('status', 'approved')->count(),
                'rejected' => Cuti::forYear($currentYear)->where('status', 'rejected')->count(),
                'total_pegawai' => Pegawai::count(),
            ];
        }

        return view('dashboard.index', compact('cutis', 'stats', 'user', 'currentYear'));
    }

    /**
     * Switch / Toggle Fitur Simpanan Jam Lembur (Khusus HRD)
     */
    public function toggleLembur()
    {
        if (!Auth::user()->isHrd()) {
            abort(403, 'Hanya HRD yang berhak mengubah pengaturan fitur sistem.');
        }

        $current = Setting::isLemburEnabled();
        Setting::set('feature_lembur', !$current);
        $statusText = !$current ? 'DIAKTIFKAN' : 'DINONAKTIFKAN';

        return back()->with('success', "Fitur Simpanan Jam Lembur & Cuti Kompensasi berhasil {$statusText}!");
    }
}
