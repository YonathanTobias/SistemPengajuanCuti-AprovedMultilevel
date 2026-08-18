<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\Divisi;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportController extends Controller
{
    private function authorizeHrd()
    {
        if (!Auth::user() || !Auth::user()->isHrd()) {
            abort(403, 'Akses Ditolak: Fitur Laporan & Export Cuti hanya diperuntukkan bagi HRD.');
        }
    }

    private function getAvailableYears()
    {
        $years = Cuti::selectRaw('strftime("%Y", tanggal_mulai) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        return !empty($years) ? $years : [date('Y')];
    }

    public function index(Request $request)
    {
        $this->authorizeHrd();

        $availableYears = $this->getAvailableYears();
        $selectedYear = $request->input('tahun', '');

        $query = Cuti::with(['pegawai.divisi'])->orderBy('created_at', 'desc');

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_mulai', $request->tahun);
        }

        if ($request->filled('pegawai_id')) {
            $query->where('pegawai_id', $request->pegawai_id);
        }

        if ($request->filled('divisi_id')) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('divisi_id', $request->divisi_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tgl_awal')) {
            $query->whereDate('tanggal_mulai', '>=', $request->tgl_awal);
        }

        if ($request->filled('tgl_akhir')) {
            $query->whereDate('tanggal_selesai', '<=', $request->tgl_akhir);
        }

        $cutis = $query->get();
        $pegawais = Pegawai::orderBy('nama', 'asc')->get();
        $divisis = Divisi::orderBy('nama_divisi', 'asc')->get();

        return view('reports.index', compact('cutis', 'pegawais', 'divisis', 'availableYears', 'selectedYear'));
    }

    public function exportCsv(Request $request)
    {
        $this->authorizeHrd();

        $query = Cuti::with(['pegawai.divisi'])->orderBy('created_at', 'desc');

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_mulai', $request->tahun);
        }

        if ($request->filled('pegawai_id')) {
            $query->where('pegawai_id', $request->pegawai_id);
        }

        if ($request->filled('divisi_id')) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('divisi_id', $request->divisi_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tgl_awal')) {
            $query->whereDate('tanggal_mulai', '>=', $request->tgl_awal);
        }

        if ($request->filled('tgl_akhir')) {
            $query->whereDate('tanggal_selesai', '<=', $request->tgl_akhir);
        }

        $cutis = $query->get();

        $yearSuffix = $request->filled('tahun') ? "_Tahun_{$request->tahun}" : '';
        $filename = "Laporan_Cuti_STIKes_Panti_Waluya{$yearSuffix}_" . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($cutis) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'No', 'Kode Tracking', 'NIP', 'Nama Pegawai', 'Divisi / Prodi', 'Jabatan',
                'Jenis Cuti', 'Tanggal Cuti', 'Jumlah Hari', 'Status Cuti', 'Alasan Cuti',
                'Alamat Cuti', 'No HP Cuti', 'Tgl Disetujui Kadiv', 'Tgl Disetujui HRD', 'Tgl Disetujui Ketua'
            ]);

            foreach ($cutis as $index => $c) {
                $statusLabel = match($c->status) {
                    'pending_kadiv' => 'Menunggu Approval Kadiv',
                    'pending_hrd' => 'Menunggu Approval HRD',
                    'pending_ketua' => 'Menunggu Approval Ketua STIKes',
                    'approved' => 'Disetujui Sepenuhnya (Approved)',
                    'rejected' => 'Ditolak (Rejected)',
                    default => $c->status
                };

                fputcsv($file, [
                    $index + 1,
                    $c->kode_tracking,
                    $c->pegawai->nip ?? '-',
                    $c->pegawai->nama ?? '-',
                    $c->pegawai->divisi->nama_divisi ?? '-',
                    $c->pegawai->jabatan ?? '-',
                    $c->jenis_cuti,
                    $c->tanggal_mulai ? $c->tanggal_mulai->format('d/m/Y') : '-',
                    $c->jumlah_hari,
                    $statusLabel,
                    $c->alasan,
                    $c->alamat_cuti,
                    $c->no_hp_cuti,
                    $c->kadiv_approved_at ? $c->kadiv_approved_at->format('d/m/Y H:i') : '-',
                    $c->hrd_approved_at ? $c->hrd_approved_at->format('d/m/Y H:i') : '-',
                    $c->ketua_approved_at ? $c->ketua_approved_at->format('d/m/Y H:i') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportXlsx(Request $request)
    {
        $this->authorizeHrd();

        $query = Cuti::with(['pegawai.divisi'])->orderBy('created_at', 'desc');

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_mulai', $request->tahun);
        }

        if ($request->filled('pegawai_id')) {
            $query->where('pegawai_id', $request->pegawai_id);
        }

        if ($request->filled('divisi_id')) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('divisi_id', $request->divisi_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tgl_awal')) {
            $query->whereDate('tanggal_mulai', '>=', $request->tgl_awal);
        }

        if ($request->filled('tgl_akhir')) {
            $query->whereDate('tanggal_selesai', '<=', $request->tgl_akhir);
        }

        $cutis = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Cuti');

        $sheet->mergeCells('A1:Q1');
        $yearTitle = $request->filled('tahun') ? " TAHUN {$request->tahun}" : "";
        $sheet->setCellValue('A1', 'REKAPITULASI DATA CUTI PEGAWAI' . $yearTitle . ' - STIKES PANTI WALUYA MALANG');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:Q2');
        $sheet->setCellValue('A2', 'Tanggal Cetak: ' . date('d F Y H:i') . ' WIB');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = [
            'No', 'Kode Tracking', 'NIP', 'Nama Pegawai', 'Divisi / Prodi', 'Jabatan',
            'Jenis Cuti', 'Tgl Cuti', 'Tgl Selesai', 'Durasi (Hari)', 'Status Cuti',
            'Alasan Cuti', 'Alamat Cuti', 'No. HP', 'Disetujui Kadiv', 'Disetujui HRD', 'Disetujui Ketua'
        ];

        $rowNum = 4;
        $colIndex = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($colIndex . $rowNum, $h);
            $colIndex++;
        }

        $headerRange = 'A4:Q4';
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('1E3A8A');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowNum = 5;
        foreach ($cutis as $index => $c) {
            $statusLabel = match($c->status) {
                'pending_kadiv' => 'Menunggu Approval Kadiv',
                'pending_hrd' => 'Menunggu Approval HRD',
                'pending_ketua' => 'Menunggu Approval Ketua STIKes',
                'approved' => 'Disetujui (Approved)',
                'rejected' => 'Ditolak (Rejected)',
                default => $c->status
            };

            $sheet->setCellValue('A' . $rowNum, $index + 1);
            $sheet->setCellValue('B' . $rowNum, $c->kode_tracking);
            $sheet->setCellValue('C' . $rowNum, $c->pegawai->nip ?? '-');
            $sheet->setCellValue('D' . $rowNum, $c->pegawai->nama ?? '-');
            $sheet->setCellValue('E' . $rowNum, $c->pegawai->divisi->nama_divisi ?? '-');
            $sheet->setCellValue('F' . $rowNum, $c->pegawai->jabatan ?? '-');
            $sheet->setCellValue('G' . $rowNum, $c->jenis_cuti);
            $sheet->setCellValue('H' . $rowNum, $c->tanggal_mulai ? $c->tanggal_mulai->format('d/m/Y') : '-');
            $sheet->setCellValue('I' . $rowNum, $c->tanggal_selesai ? $c->tanggal_selesai->format('d/m/Y') : '-');
            $sheet->setCellValue('J' . $rowNum, $c->jumlah_hari);
            $sheet->setCellValue('K' . $rowNum, $statusLabel);
            $sheet->setCellValue('L' . $rowNum, $c->alasan);
            $sheet->setCellValue('M' . $rowNum, $c->alamat_cuti);
            $sheet->setCellValue('N' . $rowNum, $c->no_hp_cuti);
            $sheet->setCellValue('O' . $rowNum, $c->kadiv_approved_at ? $c->kadiv_approved_at->format('d/m/Y H:i') : '-');
            $sheet->setCellValue('P' . $rowNum, $c->hrd_approved_at ? $c->hrd_approved_at->format('d/m/Y H:i') : '-');
            $sheet->setCellValue('Q' . $rowNum, $c->ketua_approved_at ? $c->ketua_approved_at->format('d/m/Y H:i') : '-');

            $rowNum++;
        }

        $dataRange = 'A4:Q' . ($rowNum - 1);
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $yearSuffix = $request->filled('tahun') ? "_Tahun_{$request->tahun}" : '';
        $filename = "Laporan_Cuti_STIKes_Panti_Waluya{$yearSuffix}_" . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportPegawaiCsv(Pegawai $pegawai)
    {
        $this->authorizeHrd();

        $cutis = Cuti::with(['pegawai.divisi'])
            ->where('pegawai_id', $pegawai->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'Cuti_Pegawai_' . preg_replace('/[^A-Za-z0-9]/', '_', $pegawai->nama) . '_' . date('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($cutis, $pegawai) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['RIWAYAT CUTI PEGAWAI - STIKES PANTI WALUYA MALANG']);
            fputcsv($file, ['Nama Pegawai', $pegawai->nama]);
            fputcsv($file, ['NIP', $pegawai->nip]);
            fputcsv($file, ['Divisi/Prodi', $pegawai->divisi->nama_divisi ?? '-']);
            fputcsv($file, ['Sisa Cuti Tahunan', $pegawai->sisa_cuti . ' dari ' . $pegawai->jatah_cuti . ' hari']);
            fputcsv($file, []);

            fputcsv($file, [
                'No', 'Kode Tracking', 'Jenis Cuti', 'Tanggal Cuti', 'Jumlah Hari',
                'Status', 'Alasan', 'Alamat Cuti', 'No HP',
            ]);

            foreach ($cutis as $index => $c) {
                fputcsv($file, [
                    $index + 1,
                    $c->kode_tracking,
                    $c->jenis_cuti,
                    $c->tanggal_mulai ? $c->tanggal_mulai->format('d/m/Y') : '-',
                    $c->jumlah_hari,
                    strtoupper($c->status),
                    $c->alasan,
                    $c->alamat_cuti,
                    $c->no_hp_cuti,
                ]);
            }

            fclose($file);
        };

        return response()->streamCallback($callback, 200, $headers);
    }

    public function exportPegawaiXlsx(Pegawai $pegawai)
    {
        $this->authorizeHrd();

        $cutis = Cuti::with(['pegawai.divisi'])
            ->where('pegawai_id', $pegawai->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Cuti');

        $sheet->setCellValue('A1', 'RIWAYAT CUTI PEGAWAI - STIKES PANTI WALUYA MALANG');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('A3', 'Nama Pegawai:');
        $sheet->setCellValue('B3', $pegawai->nama);
        $sheet->setCellValue('A4', 'NIP:');
        $sheet->setCellValue('B4', $pegawai->nip);
        $sheet->setCellValue('A5', 'Divisi / Prodi:');
        $sheet->setCellValue('B5', $pegawai->divisi->nama_divisi ?? '-');
        $sheet->setCellValue('A6', 'Sisa Cuti Tahunan:');
        $sheet->setCellValue('B6', $pegawai->sisa_cuti . ' Hari (dari total ' . $pegawai->jatah_cuti . ' hari)');

        $sheet->getStyle('A3:A6')->getFont()->setBold(true);

        $headers = ['No', 'Kode Tracking', 'Jenis Cuti', 'Tgl Cuti', 'Jumlah Hari', 'Status Cuti', 'Alasan', 'Alamat Cuti', 'No HP'];
        $rowNum = 8;
        $colIndex = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($colIndex . $rowNum, $h);
            $colIndex++;
        }

        $sheet->getStyle('A8:I8')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A8:I8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('0D9488');

        $rowNum = 9;
        foreach ($cutis as $index => $c) {
            $sheet->setCellValue('A' . $rowNum, $index + 1);
            $sheet->setCellValue('B' . $rowNum, $c->kode_tracking);
            $sheet->setCellValue('C' . $rowNum, $c->jenis_cuti);
            $sheet->setCellValue('D' . $rowNum, $c->tanggal_mulai ? $c->tanggal_mulai->format('d/m/Y') : '-');
            $sheet->setCellValue('E' . $rowNum, $c->jumlah_hari);
            $sheet->setCellValue('F' . $rowNum, strtoupper($c->status));
            $sheet->setCellValue('G' . $rowNum, $c->alasan);
            $sheet->setCellValue('H' . $rowNum, $c->alamat_cuti);
            $sheet->setCellValue('I' . $rowNum, $c->no_hp_cuti);
            $rowNum++;
        }

        $sheet->getStyle('A8:I' . ($rowNum - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $safeName = preg_replace('/[^A-Za-z0-9]/', '_', $pegawai->nama);
        $filename = 'Cuti_Pegawai_' . $safeName . '_' . date('Ymd') . '.xlsx';

        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $this->authorizeHrd();

        $query = Cuti::with(['pegawai.divisi'])->orderBy('created_at', 'desc');

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_mulai', $request->tahun);
        }

        if ($request->filled('pegawai_id')) {
            $query->where('pegawai_id', $request->pegawai_id);
        }

        if ($request->filled('divisi_id')) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('divisi_id', $request->divisi_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tgl_awal')) {
            $query->whereDate('tanggal_mulai', '>=', $request->tgl_awal);
        }

        if ($request->filled('tgl_akhir')) {
            $query->whereDate('tanggal_selesai', '<=', $request->tgl_akhir);
        }

        $cutis = $query->get();

        return view('reports.pdf', compact('cutis'));
    }
}
