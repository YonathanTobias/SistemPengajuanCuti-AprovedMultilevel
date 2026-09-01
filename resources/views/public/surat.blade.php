<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin Cuti - {{ $cuti->kode_tracking }}</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Times+New+Roman&family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #F8FAFC;
        }
        @media print {
            body { background: white; }
            .no-print { display: none !important; }
            .print-card { shadow: none; border: none; margin: 0; padding: 0; }
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    <!-- Action Bar (Hidden on print) -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('public.tracking', ['kode' => $cuti->kode_tracking]) }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-lg text-sm font-semibold flex items-center gap-2">
            &larr; Kembali ke Pelacakan
        </a>
        <button onclick="window.print()" class="px-6 py-2.5 bg-blue-900 hover:bg-blue-800 text-white rounded-lg text-sm font-bold shadow flex items-center gap-2">
            🖨️ Cetak / Download PDF
        </button>
    </div>

    <!-- Paper Container -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-xl shadow-xl border border-slate-200 text-slate-900 print-card">
        
        <!-- Header Kop Surat (With Official Logo) -->
        <div class="border-b-4 border-double border-slate-900 pb-4 mb-6 relative flex items-center gap-6">
            <img src="{{ asset('images/logo.png') }}" alt="Logo STIKes" class="h-24 w-auto object-contain shrink-0">
            <div class="text-center flex-grow">
                <div class="text-xl sm:text-2xl font-black uppercase tracking-wider text-blue-950 leading-tight">SEKOLAH TINGGI ILMU KESEHATAN (STIKes)</div>
                <div class="text-2xl sm:text-3xl font-black uppercase tracking-wider text-blue-950 leading-tight mt-1">PANTI WALUYA MALANG</div>
                <div class="text-xs italic text-slate-700 mt-1.5">Jl. Yulius Usman No. 62, Malang, Jawa Timur | Telp: (0341) 369003</div>
                <div class="text-xs text-slate-600">Website: www.stikespantiwaluya.ac.id | Email: stikes.pantiwaluyamlg@gmail.com</div>
            </div>
        </div>

        <!-- Surat Title -->
        <div class="text-center my-6">
            <h1 class="text-lg font-bold uppercase tracking-wider underline">SURAT IZIN CUTI PEGAWAI (1 HARI)</h1>
            <p class="text-xs font-mono mt-1">Nomor: {{ $cuti->kode_tracking }}/STIKES-PW/CUTI/{{ date('Y') }}</p>
        </div>

        <!-- Body Content -->
        <div class="space-y-4 text-sm leading-relaxed text-justify">
            <p>Yang bertanda tangan di bawah ini Ketua STIKes Panti Waluya Malang, memberikan izin cuti kepada pegawai dengan data sebagai berikut:</p>

            <table class="w-full text-sm border-collapse my-4">
                <tr>
                    <td class="py-1 w-48 font-semibold">Nama Pegawai</td>
                    <td class="py-1 w-4">:</td>
                    <td class="py-1 font-bold">{{ $cuti->pegawai->nama }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold">NIP / NIK</td>
                    <td class="py-1">:</td>
                    <td class="py-1 font-mono">{{ $cuti->pegawai->nip }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold">Divisi / Program Studi</td>
                    <td class="py-1">:</td>
                    <td class="py-1">{{ $cuti->pegawai->divisi->nama_divisi ?? '-' }}</td>
                </tr>

                <tr>
                    <td class="py-1 font-semibold">Jenis Cuti</td>
                    <td class="py-1">:</td>
                    <td class="py-1 font-bold text-blue-900">{{ $cuti->jenis_cuti }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold">Tanggal Pelaksanaan Cuti</td>
                    <td class="py-1">:</td>
                    <td class="py-1 font-bold">1 Hari Kerja (Tanggal: {{ $cuti->tanggal_mulai->translatedFormat('d F Y') }})</td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold">Alasan / Keperluan Cuti</td>
                    <td class="py-1">:</td>
                    <td class="py-1 italic">"{{ $cuti->alasan ?? 'Cuti Tahunan Pegawai' }}"</td>
                </tr>
            </table>

            <p>Demikian Surat Izin Cuti ini dibuat untuk dapat dipergunakan sebagaimana mestinya dan setelah selesai menjalankan cuti yang bersangkutan diwajibkan melapor kembali kepada atasan langsung.</p>
        </div>

        <!-- Footer Verification Code -->
        <div class="mt-12 pt-4 border-t border-slate-200 flex items-center justify-between text-[10px] text-slate-500 font-mono">
            <div>Dokumen Resmi STIKes Panti Waluya Malang &bull; Autentikasi Sistem Online</div>
            <div>Ref Code: {{ md5($cuti->kode_tracking) }}</div>
        </div>
    </div>
</body>
</html>
