<?php

namespace App\Filament\Widgets;

use App\Models\KodeRekening;
use App\Models\PenerimaanPajak;
use App\Models\TahunAnggaran;
use App\Models\TargetPajak;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class TargetRealisasiWidget extends ChartWidget
{
    protected static ?string $heading = 'Tren Penerimaan Bulanan';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    
    protected function getData(): array
    {
        $tahunAnggaranAktif = TahunAnggaran::where('aktif', true)->first();
        
        if (!$tahunAnggaranAktif) {
            return [
                'datasets' => [
                    [
                        'label' => 'Realisasi',
                        'data' => array_fill(0, 12, 0),
                    ],
                ],
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            ];
        }
        
        $tahun = $tahunAnggaranAktif->tahun;
        $namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        // Data Realisasi per bulan
        $realisasiData = [];
        for ($i = 1; $i <= 12; $i++) {
            $startDate = Carbon::createFromDate($tahun, $i, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($tahun, $i, 1)->endOfMonth();
            
            $realisasiBulan = PenerimaanPajak::whereHas('targetPajak', function ($query) use ($tahunAnggaranAktif) {
                    $query->where('tahun_anggaran_id', $tahunAnggaranAktif->id);
                })
                ->whereBetween('tanggal_penerimaan', [$startDate, $endDate])
                ->sum('nilai_penerimaan');
                
            $realisasiData[] = $realisasiBulan / 1000000; // Convert to millions
        }
        
        // Total pagu anggaran
        $totalPaguAnggaran = TargetPajak::where('tahun_anggaran_id', $tahunAnggaranAktif->id)
            ->sum('pagu_anggaran');
            
        // Data Target per bulan
        $targetData = [];
        $currentMonth = Carbon::now()->month;
        
        for ($i = 1; $i <= 12; $i++) {
            if ($i <= $currentMonth) {
                $targetPercentage = $tahunAnggaranAktif->getTargetForBulan($i);
                $targetData[] = ($totalPaguAnggaran * $targetPercentage / 100) / 1000000; // Convert to millions
            } else {
                $targetData[] = null; // Null for future months
            }
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Realisasi',
                    'data' => $realisasiData,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'borderWidth' => 2,
                    'type' => 'bar',
                ],
                [
                    'label' => 'Target',
                    'data' => $targetData,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'borderWidth' => 2,
                    'type' => 'bar',
                ],
            ],
            'labels' => $namaBulan,
        ];
    }
    
    protected function getType(): string
    {
        return 'bar';
    }
}