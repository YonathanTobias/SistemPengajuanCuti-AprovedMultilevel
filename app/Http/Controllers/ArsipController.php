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

        // Get all available years from DB cutis table
        $availableYears = Cuti::selectRaw('strftime("%Y", tanggal_mulai) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [date('Y')];
        }

        // Selected year (default to first available year or previous year if current is empty)
        $selectedYear = $request->input('tahun', $availableYears[0]);

        $query = Cuti::with(['pegawai.divisi'])
            ->whereYear('tanggal_mulai', $selectedYear)
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
            })->orWhere('kode_tracking', 'like', "%{$search}%");
        }

        $cutis = $query->paginate(15)->withQueryString();
        $divisis = Divisi::orderBy('nama_divisi', 'asc')->get();

        // Archived Year Statistics
        $statsQuery = Cuti::whereYear('tanggal_mulai', $selectedYear);
        if ($user->isKadiv() && $user->divisi_id) {
            $statsQuery->whereHas('pegawai', fn($q) => $q->where('divisi_id', $user->divisi_id));
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'approved' => (clone $statsQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $statsQuery)->where('status', 'rejected')->count(),
            'pending' => (clone $statsQuery)->where('status', 'like', 'pending%')->count(),
        ];

        return view('arsip.index', compact('cutis', 'availableYears', 'selectedYear', 'divisis', 'stats', 'user'));
    }
}
