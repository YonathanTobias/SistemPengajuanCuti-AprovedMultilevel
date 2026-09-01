<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\Divisi;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get all available years from DB cutis table (considering tahun_cuti and tanggal_mulai)
        $availableYears = Cuti::selectRaw('DISTINCT COALESCE(tahun_cuti, CAST(strftime("%Y", tanggal_mulai) AS INTEGER)) as year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [date('Y')];
        }

        // Selected year (default to first available year)
        $selectedYear = $request->input('tahun', $availableYears[0]);

        $query = Cuti::with(['pegawai.divisi'])
            ->forYear($selectedYear)
            ->orderBy('tanggal_mulai', 'desc');

        // Role-based scoping for Kadiv
        if ($user->isKadiv() && $user->divisi_id) {
            $query->whereHas('pegawai', function ($q) use ($user) {
                $q->where('divisi_id', $user->divisi_id);
            });
        }

        // Divisi Filter
        if ($request->filled('divisi_id')) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('divisi_id', $request->divisi_id);
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pegawai', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $cutis = $query->paginate(15)->withQueryString();

        // Statistics for Selected Year
        $statQuery = Cuti::forYear($selectedYear);
        if ($user->isKadiv() && $user->divisi_id) {
            $statQuery->whereHas('pegawai', fn($q) => $q->where('divisi_id', $user->divisi_id));
        }

        $totalPengajuan = (clone $statQuery)->count();
        $totalApproved = (clone $statQuery)->where('status', 'approved')->count();
        $totalRejected = (clone $statQuery)->where('status', 'rejected')->count();
        $totalHariCuti = (clone $statQuery)->where('status', 'approved')->sum('jumlah_hari');

        $divisis = Divisi::orderBy('nama_divisi', 'asc')->get();

        return view('arsip.index', compact(
            'cutis',
            'availableYears',
            'selectedYear',
            'totalPengajuan',
            'totalApproved',
            'totalRejected',
            'totalHariCuti',
            'divisis'
        ));
    }
}
