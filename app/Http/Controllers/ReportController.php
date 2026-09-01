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
        $years = Cuti::selectRaw('DISTINCT COALESCE(tahun_cuti, CAST(strftime("%Y", tanggal_mulai) AS INTEGER)) as year')
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
            $query->forYear($request->tahun);
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

        $cutis = $query->paginate(15)->withQueryString();

        $divisis = Divisi::orderBy('nama_divisi', 'asc')->get();
        $pegawais = Pegawai::orderBy('nama', 'asc')->get();

        return view('reports.index', compact('cutis', 'availableYears', 'divisis', 'pegawais', 'selectedYear'));
    }

    public function exportCsv(Request $request)
    {
        $this->authorizeHrd();

        $query = Cuti::with(['pegawai.divisi'])->orderBy('created_at', 'desc');

        if ($request->filled('tahun')) {
            $query->forYear($request->tahun);
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
            $query->forYear($request->tahun);
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

        $headerRow = 4;
        foreach ($headers as $colIndex => $headerText) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue("{$colLetter}{$headerRow}", $headerText);
        }

        $sheet->getStyle("A{$headerRow}:Q{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $rowNum = 5;
        foreach ($cutis as $index => $c) {
            $statusLabel = match($c->status) {
                'pending_kadiv' => 'Menunggu Kadiv',
                'pending_hrd' => 'Menunggu HRD',
                'pending_ketua' => 'Menunggu Ketua',
                'approved' => 'Disetujui (Approved)',
                'rejected' => 'Ditolak (Rejected)',
                default => $c->status
            };

            $sheet->setCellValue("A{$rowNum}", $index + 1);
            $sheet->setCellValue("B{$rowNum}", $c->kode_tracking);
            $sheet->setCellValue("C{$rowNum}", $c->pegawai->nip ?? '-');
            $sheet->setCellValue("D{$rowNum}", $c->pegawai->nama ?? '-');
            $sheet->setCellValue("E{$rowNum}", $c->pegawai->divisi->nama_divisi ?? '-');
            $sheet->setCellValue("F{$rowNum}", $c->pegawai->jabatan ?? '-');
            $sheet->setCellValue("G{$rowNum}", $c->jenis_cuti);
            $sheet->setCellValue("H{$rowNum}", $c->tanggal_mulai ? $c->tanggal_mulai->format('d/m/Y') : '-');
            $sheet->setCellValue("I{$rowNum}", $c->tanggal_selesai ? $c->tanggal_selesai->format('d/m/Y') : '-');
            $sheet->setCellValue("J{$rowNum}", $c->jumlah_hari);
            $sheet->setCellValue("K{$rowNum}", $statusLabel);
            $sheet->setCellValue("L{$rowNum}", $c->alasan);
            $sheet->setCellValue("M{$rowNum}", $c->alamat_cuti);
            $sheet->setCellValue("N{$rowNum}", $c->no_hp_cuti);
            $sheet->setCellValue("O{$rowNum}", $c->kadiv_approved_at ? $c->kadiv_approved_at->format('d/m/Y H:i') : '-');
            $sheet->setCellValue("P{$rowNum}", $c->hrd_approved_at ? $c->hrd_approved_at->format('d/m/Y H:i') : '-');
            $sheet->setCellValue("Q{$rowNum}", $c->ketua_approved_at ? $c->ketua_approved_at->format('d/m/Y H:i') : '-');

            if ($rowNum % 2 == 0) {
                $sheet->getStyle("A{$rowNum}:Q{$rowNum}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
            }

            $rowNum++;
        }

        $lastRow = $rowNum - 1;
        $sheet->getStyle("A4:Q{$lastRow}")->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');

        foreach (range(1, 17) as $colIndex) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $filename = "Laporan_Cuti_STIKes_Panti_Waluya{$yearSuffix}_" . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPdf(Request $request)
    {
        $this->authorizeHrd();

        $query = Cuti::with(['pegawai.divisi'])->orderBy('created_at', 'desc');

        if ($request->filled('tahun')) {
            $query->forYear($request->tahun);
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
        $tahun = $request->input('tahun', date('Y'));

        return view('reports.pdf', compact('cutis', 'tahun'));
    }

    public function exportPegawaiCsv(Pegawai $pegawai)
    {
        $this->authorizeHrd();

        $cutis = $pegawai->cutis()->orderBy('tanggal_mulai', 'desc')->get();
        $filename = "Riwayat_Cuti_" . \Illuminate\Support\Str::slug($pegawai->nama) . "_" . date('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($cutis, $pegawai) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['REKAPITULASI CUTI INDIVIDU PEGAWAI - STIKES PANTI WALUYA MALANG']);
            fputcsv($file, ['Nama Pegawai', $pegawai->nama]);
            fputcsv($file, ['NIP', $pegawai->nip]);
            fputcsv($file, ['Divisi / Prodi', $pegawai->divisi->nama_divisi ?? '-']);
            fputcsv($file, ['Jabatan', $pegawai->jabatan]);
            fputcsv($file, ['Jatah Cuti Tahunan', $pegawai->jatah_cuti . ' Hari']);
            fputcsv($file, ['Sisa Cuti Saat Ini', $pegawai->sisa_cuti . ' Hari']);
            fputcsv($file, []);

            fputcsv($file, [
                'No', 'Kode Tracking', 'Jenis Cuti', 'Tanggal Cuti', 'Durasi (Hari)', 'Status Cuti', 'Alasan Cuti', 'Tgl Disetujui Akhir'
            ]);

            foreach ($cutis as $index => $c) {
                $statusLabel = match($c->status) {
                    'pending_kadiv' => 'Menunggu Kadiv',
                    'pending_hrd' => 'Menunggu HRD',
                    'pending_ketua' => 'Menunggu Ketua',
                    'approved' => 'Disetujui Sepenuhnya',
                    'rejected' => 'Ditolak',
                    default => $c->status
                };

                fputcsv($file, [
                    $index + 1,
                    $c->kode_tracking,
                    $c->jenis_cuti,
                    $c->tanggal_mulai ? $c->tanggal_mulai->format('d/m/Y') : '-',
                    $c->jumlah_hari,
                    $statusLabel,
                    $c->alasan,
                    $c->ketua_approved_at ? $c->ketua_approved_at->format('d/m/Y H:i') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPegawaiXlsx(Pegawai $pegawai)
    {
        $this->authorizeHrd();

        $cutis = $pegawai->cutis()->orderBy('tanggal_mulai', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Cuti');

        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'REKAPITULASI CUTI INDIVIDU PEGAWAI');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', 'STIKES PANTI WALUYA MALANG');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A4', 'Nama Pegawai:');
        $sheet->setCellValue('B4', $pegawai->nama);
        $sheet->setCellValue('A5', 'NIP:');
        $sheet->setCellValue('B5', $pegawai->nip);
        $sheet->setCellValue('A6', 'Divisi / Prodi:');
        $sheet->setCellValue('B6', $pegawai->divisi->nama_divisi ?? '-');
        $sheet->setCellValue('E4', 'Jatah Cuti:');
        $sheet->setCellValue('F4', $pegawai->jatah_cuti . ' Hari');
        $sheet->setCellValue('E5', 'Sisa Cuti:');
        $sheet->setCellValue('F5', $pegawai->sisa_cuti . ' Hari');
        $sheet->getStyle('A4:A6')->getFont()->setBold(true);
        $sheet->getStyle('E4:E5')->getFont()->setBold(true);

        $headers = ['No', 'Kode Tracking', 'Jenis Cuti', 'Tanggal Cuti', 'Durasi', 'Status', 'Alasan'];
        $headerRow = 8;
        foreach ($headers as $colIndex => $h) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue("{$colLetter}{$headerRow}", $h);
        }

        $sheet->getStyle("A{$headerRow}:G{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F766E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $rowNum = 9;
        foreach ($cutis as $index => $c) {
            $sheet->setCellValue("A{$rowNum}", $index + 1);
            $sheet->setCellValue("B{$rowNum}", $c->kode_tracking);
            $sheet->setCellValue("C{$rowNum}", $c->jenis_cuti);
            $sheet->setCellValue("D{$rowNum}", $c->tanggal_mulai ? $c->tanggal_mulai->format('d/m/Y') : '-');
            $sheet->setCellValue("E{$rowNum}", $c->jumlah_hari . ' Hari');
            $sheet->setCellValue("F{$rowNum}", $c->status === 'approved' ? 'Disetujui' : ($c->status === 'rejected' ? 'Ditolak' : 'Menunggu'));
            $sheet->setCellValue("G{$rowNum}", $c->alasan);

            if ($rowNum % 2 == 0) {
                $sheet->getStyle("A{$rowNum}:G{$rowNum}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
            }
            $rowNum++;
        }

        $lastRow = $rowNum - 1;
        $sheet->getStyle("A8:G{$lastRow}")->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');

        foreach (range(1, 7) as $colIndex) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $filename = "Riwayat_Cuti_" . \Illuminate\Support\Str::slug($pegawai->nama) . "_" . date('Ymd_His') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
