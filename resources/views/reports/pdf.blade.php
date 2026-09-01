<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Cuti Pegawai - STIKes Panti Waluya Malang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Times+New+Roman&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #F8FAFC;
        }
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none !important; }
            .print-card { shadow: none; border: none; margin: 0; padding: 0; }
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    <!-- Action Bar (Hidden on print) -->
    <div class="max-w-6xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-lg text-xs font-bold flex items-center gap-2">
            &larr; Kembali ke Laporan
        </a>
        <button onclick="window.print()" class="px-6 py-2.5 bg-blue-900 hover:bg-blue-800 text-white rounded-lg text-xs font-bold shadow flex items-center gap-2">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <!-- Paper Container -->
    <div class="max-w-6xl mx-auto bg-white p-8 sm:p-12 rounded-xl shadow-xl border border-slate-200 text-slate-900 print-card">
        
        <!-- Header Kop Surat (With Official Logo) -->
        <div class="border-b-4 border-double border-slate-900 pb-4 mb-6 flex items-center gap-6">
            <img src="{{ asset('images/logo.png') }}" alt="Logo STIKes" class="h-20 w-auto object-contain shrink-0">
            <div class="text-center flex-grow">
                <div class="text-lg sm:text-xl font-black uppercase tracking-wider text-blue-950 leading-tight">SEKOLAH TINGGI ILMU KESEHATAN (STIKes)</div>
                <div class="text-xl sm:text-2xl font-black uppercase tracking-wider text-blue-950 leading-tight mt-1">PANTI WALUYA MALANG</div>
                <div class="text-xs italic text-slate-700 mt-1">Jl. Yulius Usman No. 62, Malang, Jawa Timur | Telp: (0341) 369003</div>
                <div class="text-xs text-slate-600">Website: www.stikespantiwaluya.ac.id | Email: stikes.pantiwaluyamlg@gmail.com</div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center my-6">
            <h1 class="text-lg font-bold uppercase tracking-wider underline">LAPORAN REKAPITULASI PENGAJUAN CUTI PEGAWAI</h1>
            <p class="text-xs text-slate-600 mt-1">Tanggal Cetak: {{ date('d F Y, H:i') }} WIB</p>
        </div>

        <!-- Table -->
        <table class="w-full text-xs border-collapse border border-slate-400 my-6">
            <thead>
                <tr class="bg-slate-200 text-slate-900 font-bold border-b border-slate-400 text-center">
                    <th class="border border-slate-400 p-2">No</th>
                    <th class="border border-slate-400 p-2">Kode Tracking</th>
                    <th class="border border-slate-400 p-2">NIP / Nama Pegawai</th>
                    <th class="border border-slate-400 p-2">Divisi / Prodi</th>
                    <th class="border border-slate-400 p-2">Jenis Cuti</th>
                    <th class="border border-slate-400 p-2">Tanggal Cuti</th>
                    <th class="border border-slate-400 p-2">Hari</th>
                    <th class="border border-slate-400 p-2">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cutis as $index => $item)
                    <tr class="border-b border-slate-300">
                        <td class="border border-slate-300 p-2 text-center">{{ $index + 1 }}</td>
                        <td class="border border-slate-300 p-2 font-mono text-center font-bold">{{ $item->kode_tracking }}</td>
                        <td class="border border-slate-300 p-2">
                            <div class="font-bold">{{ $item->pegawai->nama }}</div>
                            <div class="text-[10px] text-slate-600 font-mono">NIP: {{ $item->pegawai->nip }}</div>
                        </td>
                        <td class="border border-slate-300 p-2">{{ $item->pegawai->divisi->nama_divisi ?? '-' }}</td>
                        <td class="border border-slate-300 p-2 font-bold">{{ $item->jenis_cuti }}</td>
                        <td class="border border-slate-300 p-2 text-center text-[10px]">
                            {{ $item->tanggal_mulai->format('d/m/Y') }}
                        </td>
                        <td class="border border-slate-300 p-2 text-center font-bold">{{ $item->jumlah_hari }}</td>
                        <td class="border border-slate-300 p-2 text-center font-bold">
                            @if($item->status === 'approved')
                                Disetujui
                            @elseif($item->status === 'rejected')
                                Ditolak
                            @else
                                {{ strtoupper($item->status) }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-4 text-center italic text-slate-500">Tidak ada data cuti.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer Notice -->
        <div class="mt-8 pt-4 border-t border-slate-200 text-center text-[10px] text-slate-500 italic">
            Dokumen Rekapitulasi Resmi STIKes Panti Waluya Malang &bull; Autentikasi Sistem Informasi Kepegawaian Online
        </div>

    </div>
</body>
</html>
