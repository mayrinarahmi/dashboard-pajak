<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\TahunAnggaran;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TahunAnggaranSeeder extends Seeder
{
    public function run(): void
    {
        $tahunSekarang = Carbon::now()->year;
        
        TahunAnggaran::create([
            'tahun' => $tahunSekarang,
            'tanggal_mulai' => Carbon::createFromDate($tahunSekarang, 1, 1)->startOfDay(),
            'tanggal_selesai' => Carbon::createFromDate($tahunSekarang, 12, 31)->endOfDay(),
            'aktif' => true,
        ]);
    }
}
