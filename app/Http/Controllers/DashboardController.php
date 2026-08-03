<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\Divisi;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Cuti::with(['pegawai.divisi'])->orderBy('created_at', 'desc');

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

        // Statistics
        if ($user->isKadiv()) {
            $divisiId = $user->divisi_id;
            $stats = [
                'pending' => Cuti::whereHas('pegawai', fn($q) => $q->where('divisi_id', $divisiId))
                                ->where('status', 'pending_kadiv')->count(),
                'approved' => Cuti::whereHas('pegawai', fn($q) => $q->where('divisi_id', $divisiId))
                                ->where('status', 'approved')->count(),
                'rejected' => Cuti::whereHas('pegawai', fn($q) => $q->where('divisi_id', $divisiId))
                                ->where('status', 'rejected')->count(),
                'total_pegawai' => Pegawai::where('divisi_id', $divisiId)->count(),
            ];
        } elseif ($user->isHrd()) {
            $stats = [
                'pending_kadiv' => Cuti::where('status', 'pending_kadiv')->count(),
                'pending_hrd' => Cuti::where('status', 'pending_hrd')->count(),
                'pending_ketua' => Cuti::where('status', 'pending_ketua')->count(),
                'approved' => Cuti::where('status', 'approved')->count(),
                'rejected' => Cuti::where('status', 'rejected')->count(),
                'total_pegawai' => Pegawai::count(),
                'total_divisi' => Divisi::count(),
            ];
        } else { // Ketua STIKes
            $stats = [
                'pending_ketua' => Cuti::where('status', 'pending_ketua')->count(),
                'approved' => Cuti::where('status', 'approved')->count(),
                'rejected' => Cuti::where('status', 'rejected')->count(),
                'total_pegawai' => Pegawai::count(),
            ];
        }

        return view('dashboard.index', compact('cutis', 'stats', 'user'));
    }
}
