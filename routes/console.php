<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Otomatis reset kuota sisa_cuti pegawai menjadi 0 setiap 1 Januari pukul 00:00
Schedule::command('cuti:reset-kuota --quota=0')->yearlyOn(1, 1, '00:00');
