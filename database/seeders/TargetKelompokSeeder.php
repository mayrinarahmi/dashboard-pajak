<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\TahunAnggaran;
use App\Models\TargetKelompok;
use App\Models\BulanTerkait;
use Illuminate\Database\Seeder;

class TargetKelompokSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAnggaran = TahunAnggaran::where('aktif', true)->first();
        
        if (!$tahunAnggaran) {
            $this->command->info('Tidak ada tahun anggaran aktif. Silakan buat tahun anggaran terlebih dahulu.');
            return;
        }
        
        // Triwulan I + April (40%)
        $kelompok1 = TargetKelompok::create([
            'tahun_anggaran_id' => $tahunAnggaran->id,
            'nama_kelompok' => 'Triwulan I + April',
            'persentase_target' => 40,
            'keterangan' => 'Target untuk Januari, Februari, Maret, dan April',
        ]);
        
        for ($bulan = 1; $bulan <= 4; $bulan++) {
            BulanTerkait::create([
                'target_kelompok_id' => $kelompok1->id,
                'bulan' => $bulan,
            ]);
        }
        
        // Mei - Juni (20%)
        $kelompok2 = TargetKelompok::create([
            'tahun_anggaran_id' => $tahunAnggaran->id,
            'nama_kelompok' => 'Mei - Juni',
            'persentase_target' => 20,
            'keterangan' => 'Target untuk Mei dan Juni',
        ]);
        
        for ($bulan = 5; $bulan <= 6; $bulan++) {
            BulanTerkait::create([
                'target_kelompok_id' => $kelompok2->id,
                'bulan' => $bulan,
            ]);
        }
        
        // Triwulan III + Oktober (30%)
        $kelompok3 = TargetKelompok::create([
            'tahun_anggaran_id' => $tahunAnggaran->id,
            'nama_kelompok' => 'Triwulan III + Oktober',
            'persentase_target' => 30,
            'keterangan' => 'Target untuk Juli, Agustus, September, dan Oktober',
        ]);
        
        for ($bulan = 7; $bulan <= 10; $bulan++) {
            BulanTerkait::create([
                'target_kelompok_id' => $kelompok3->id,
                'bulan' => $bulan,
            ]);
        }
        
        // November - Desember (10%)
        $kelompok4 = TargetKelompok::create([
            'tahun_anggaran_id' => $tahunAnggaran->id,
            'nama_kelompok' => 'November - Desember',
            'persentase_target' => 10,
            'keterangan' => 'Target untuk November dan Desember',
        ]);
        
        for ($bulan = 11; $bulan <= 12; $bulan++) {
            BulanTerkait::create([
                'target_kelompok_id' => $kelompok4->id,
                'bulan' => $bulan,
            ]);
        }
    }
}
