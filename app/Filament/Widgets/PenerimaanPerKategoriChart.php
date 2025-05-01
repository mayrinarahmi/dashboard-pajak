<?php

namespace App\Filament\Widgets;

use App\Models\KodeRekening;
use App\Models\PenerimaanPajak;
use App\Models\TahunAnggaran;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class PenerimaanPerKategoriChart extends ChartWidget
{
    protected static ?string $heading = 'Penerimaan per Kategori';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    
    protected function getData(): array
    {
        $tahunAnggaranAktif = TahunAnggaran::where('aktif', true)->first();
        
        if (!$tahunAnggaranAktif) {
            return [
                'datasets' => [
                    [
                        'label' => 'Penerimaan Pajak',
                        'data' => [],
                    ],
                ],
                'labels' => [],
            ];
        }
        
        // Ambil data level 2
        $level2Nodes = KodeRekening::where('level', 2)
            ->orderBy('kode')
            ->get();
            
        $labels = [];
        $data = [];
        $backgroundColor = [
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 99, 132, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)',
        ];
        
        foreach ($level2Nodes as $node) {
            $totalPenerimaan = $this->getTotalPenerimaan($node->id, $tahunAnggaranAktif->id);
            
            if ($totalPenerimaan > 0) {
                $labels[] = $node->uraian;
                $data[] = $totalPenerimaan / 1000000; // Convert to millions
            }
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Penerimaan Pajak (dalam Juta Rupiah)',
                    'data' => $data,
                    'backgroundColor' => array_slice($backgroundColor, 0, count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }
    
    private function getTotalPenerimaan($kodeRekeningId, $tahunAnggaranId)
    {
        // Dapatkan semua kode rekening level 4 yang berada di bawah kode rekening ini
        $level4Nodes = $this->getLevel4Descendants($kodeRekeningId);
        
        // Hitung total penerimaan untuk semua kode rekening level 4 tersebut
        $totalPenerimaan = PenerimaanPajak::whereHas('targetPajak', function ($query) use ($level4Nodes, $tahunAnggaranId) {
            $query->whereIn('kode_rekening_id', $level4Nodes)
                  ->where('tahun_anggaran_id', $tahunAnggaranId);
        })->sum('nilai_penerimaan');
        
        return $totalPenerimaan;
    }
    
    private function getLevel4Descendants($kodeRekeningId)
    {
        // Rekursif mencari semua kode rekening level 4 yang merupakan turunan dari kode rekening ini
        $result = [];
        
        $children = KodeRekening::where('parent_id', $kodeRekeningId)->get();
        
        foreach ($children as $child) {
            if ($child->level == 4 && $child->is_pajak) {
                $result[] = $child->id;
            } else {
                $result = array_merge($result, $this->getLevel4Descendants($child->id));
            }
        }
        
        return $result;
    }
    
    protected function getType(): string
    {
        return 'pie';
    }

}