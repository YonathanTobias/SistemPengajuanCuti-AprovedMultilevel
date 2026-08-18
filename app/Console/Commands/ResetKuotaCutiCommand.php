<?php

namespace App\Console\Commands;

use App\Models\Pegawai;
use Illuminate\Console\Command;

class ResetKuotaCutiCommand extends Command
{
    protected $signature = 'cuti:reset-kuota {--quota=0 : Jumlah kuota sisa_cuti setelah reset (default 0)}';

    protected $description = 'Mereset sisa kuota cuti seluruh pegawai menjadi 0 (atau nilai tertentu) setiap tanggal 1 Januari';

    public function handle()
    {
        $newQuota = (int) $this->option('quota');

        $affected = Pegawai::query()->update([
            'sisa_cuti' => $newQuota,
        ]);

        $this->info("Berhasil mereset sisa kuota cuti untuk {$affected} pegawai menjadi {$newQuota} hari.");
        return Command::SUCCESS;
    }
}
