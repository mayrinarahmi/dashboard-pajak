<?php

namespace App\Filament\Widgets;

use App\Models\KodeRekening;
use App\Models\PenerimaanPajak;
use App\Models\TahunAnggaran;
use App\Models\TargetPajak;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class RekapitulasiPenerimaanWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $tahunAnggaranAktif = TahunAnggaran::where('aktif', true)->first();
        
        if (!$tahunAnggaranAktif) {
            return [
                Stat::make('Total Penerimaan', 'Rp 0')
                    ->description('Tidak ada tahun anggaran aktif')
                    ->descriptionIcon('heroicon-m-x-circle'),
                Stat::make('Target', '0%')
                    ->description('Tidak ada tahun anggaran aktif')
                    ->descriptionIcon('heroicon-m-x-circle'),
                Stat::make('Progress', '0%')
                    ->description('Tidak ada tahun anggaran aktif')
                    ->descriptionIcon('heroicon-m-x-circle'),
            ];
        }
        
        // Hitung total pagu anggaran
        $totalPaguAnggaran = TargetPajak::where('tahun_anggaran_id', $tahunAnggaranAktif->id)
            ->sum('pagu_anggaran');
            
        // Hitung total penerimaan tahun ini
        $totalPenerimaan = PenerimaanPajak::whereHas('targetPajak', function ($query) use ($tahunAnggaranAktif) {
                $query->where('tahun_anggaran_id', $tahunAnggaranAktif->id);
            })
            ->whereYear('tanggal_penerimaan', $tahunAnggaranAktif->tahun)
            ->sum('nilai_penerimaan');
            
        // Hitung persentase penerimaan
        $persentasePenerimaan = $totalPaguAnggaran > 0 
            ? number_format(($totalPenerimaan / $totalPaguAnggaran) * 100, 2) 
            : 0;
            
        // Ambil bulan saat ini dan target persentase
        $currentMonth = Carbon::now()->month;
        $targetPercentage = $tahunAnggaranAktif->getTargetKumulatifSampai($currentMonth);
        
        // Hitung nilai target saat ini
        $nilaiTarget = ($totalPaguAnggaran * $targetPercentage) / 100;
        
        // Hitung persentase pencapaian terhadap target
        $persentasePencapaian = $nilaiTarget > 0 
            ? number_format(($totalPenerimaan / $nilaiTarget) * 100, 2) 
            : 0;
            
        $statusTarget = 'danger';
        if ($persentasePencapaian >= 90) {
            $statusTarget = 'success';
        } elseif ($persentasePencapaian >= 70) {
            $statusTarget = 'warning';
        }
        
        return [
            Stat::make('Total Penerimaan', 'Rp ' . number_format($totalPenerimaan, 0, ',', '.'))
                ->description('Tahun ' . $tahunAnggaranAktif->tahun)
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
                
            Stat::make('Target ' . $targetPercentage . '%', 'Rp ' . number_format($nilaiTarget, 0, ',', '.'))
                ->description('Kumulatif s/d bulan ' . Carbon::createFromDate(null, $currentMonth, 1)->format('F'))
                ->descriptionIcon('heroicon-m-flag'),
                
            Stat::make('Pencapaian Target', $persentasePencapaian . '%')
                ->description(
                    $persentasePencapaian >= 100 
                        ? 'Target tercapai' 
                        : 'Kurang ' . number_format(100 - $persentasePencapaian, 2) . '%'
                )
                ->descriptionIcon(
                    $persentasePencapaian >= 100 
                        ? 'heroicon-m-check-circle' 
                        : 'heroicon-m-exclamation-circle'
                )
                ->color($statusTarget)
                ->chart([
                    min($persentasePencapaian, 25),
                    min(max($persentasePencapaian - 25, 0), 25),
                    min(max($persentasePencapaian - 50, 0), 25),
                    min(max($persentasePencapaian - 75, 0), 25),
                ]),
        ];
    }
}
