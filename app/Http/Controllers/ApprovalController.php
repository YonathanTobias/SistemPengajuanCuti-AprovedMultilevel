<?php

namespace App\Http\Controllers;

use App\Models\Cuti;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function approveKadiv(Request $request, Cuti $cuti)
    {
        $user = Auth::user();

        if (!$user->isKadiv()) {
            return back()->with('error', 'Hanya Kepala Divisi/Prodi yang dapat menyetujui tahap ini.');
        }

        if ($user->divisi_id && $cuti->pegawai->divisi_id != $user->divisi_id) {
            return back()->with('error', 'Anda hanya dapat menyetujui pengajuan cuti pegawai dari divisi Anda sendiri.');
        }

        if ($cuti->status !== 'pending_kadiv') {
            return back()->with('error', 'Pengajuan cuti ini tidak berada dalam status menunggu persetujuan Kadiv.');
        }

        $cuti->update([
            'status' => 'pending_hrd',
            'catatan_kadiv' => $request->catatan ?: 'Disetujui oleh Kepala Divisi / Prodi.',
            'kadiv_approved_at' => now(),
        ]);

        return back()->with('success', "Pengajuan cuti {$cuti->kode_tracking} disetujui oleh Kepala Divisi. Diteruskan ke HRD.");
    }

    public function approveHrd(Request $request, Cuti $cuti)
    {
        $user = Auth::user();

        if (!$user->isHrd()) {
            return back()->with('error', 'Hanya HRD yang dapat menyetujui tahap ini.');
        }

        if ($cuti->status !== 'pending_hrd') {
            return back()->with('error', 'Pengajuan cuti ini tidak berada dalam status menunggu persetujuan HRD.');
        }

        $cuti->update([
            'status' => 'pending_ketua',
            'catatan_hrd' => $request->catatan ?: 'Disetujui oleh HRD.',
            'hrd_approved_at' => now(),
        ]);

        return back()->with('success', "Pengajuan cuti {$cuti->kode_tracking} disetujui oleh HRD. Diteruskan ke Ketua STIKes.");
    }

    public function approveKetua(Request $request, Cuti $cuti)
    {
        $user = Auth::user();

        if (!$user->isKetua()) {
            return back()->with('error', 'Hanya Ketua STIKes yang dapat menyetujui tahap akhir ini.');
        }

        if ($cuti->status !== 'pending_ketua') {
            return back()->with('error', 'Pengajuan cuti ini tidak berada dalam status menunggu persetujuan Ketua STIKes.');
        }

        // Final approval: deduct leave quota if annual leave
        $pegawai = $cuti->pegawai;
        if ($cuti->jenis_cuti === 'Cuti Tahunan') {
            $pegawai->decrement('sisa_cuti', $cuti->jumlah_hari);
        }

        $cuti->update([
            'status' => 'approved',
            'catatan_ketua' => $request->catatan ?: 'Pengajuan Cuti Disetujui Sepenuhnya oleh Ketua STIKes.',
            'ketua_approved_at' => now(),
        ]);

        return back()->with('success', "Pengajuan cuti {$cuti->kode_tracking} TELAH DISETUJUI SEPENUHNYA oleh Ketua STIKes! Surat Cuti telah dapat dicetak.");
    }

    public function reject(Request $request, Cuti $cuti)
    {
        $request->validate([
            'catatan_penolakan' => 'required|string|min:5',
        ], [
            'catatan_penolakan.required' => 'Alasan penolakan wajib diisi.',
            'catatan_penolakan.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $user = Auth::user();
        
        $roleName = match($user->role) {
            'kadiv' => 'Kepala Divisi / Kaprodi',
            'hrd' => 'HRD',
            'ketua' => 'Ketua STIKes',
            default => 'Administrator'
        };

        $cuti->update([
            'status' => 'rejected',
            'rejected_by' => $user->role,
            'catatan_penolakan' => "Ditolak oleh {$roleName}: " . $request->catatan_penolakan,
        ]);

        return back()->with('info', "Pengajuan cuti {$cuti->kode_tracking} ditolak oleh {$roleName}.");
    }
}
