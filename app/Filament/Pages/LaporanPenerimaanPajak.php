<?php

namespace App\Filament\Pages;

use App\Models\KodeRekening;
use App\Models\TahunAnggaran;
use App\Models\Penerimaan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class LaporanPenerimaanPajak extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Penerimaan Pajak';
    protected static ?string $title = 'Laporan Penerimaan Pajak Daerah';
    protected static ?string $slug = 'laporan-penerimaan-pajak';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];
    public ?Collection $reportData = null;
    public ?string $tanggalLaporan = null;
    public ?string $judulLaporan = null;
    public bool $showDetailedView = false;
    
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tahun_anggaran_id')
                    ->label('Tahun Anggaran')
                    ->options(TahunAnggaran::pluck('tahun', 'id'))
                    ->required()
                    ->live(),
                    
                Select::make('tipe_laporan')
                    ->label('Tipe Laporan')
                    ->options([
                        'custom' => 'Rentang Tanggal Kustom',
                        'monthly' => 'Bulanan',
                        'quarterly' => 'Triwulanan',
                        'yearly' => 'Tahunan',
                    ])
                    ->default('custom')
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('tanggal_mulai', now()->startOfMonth()->format('Y-m-d'))),
                    
                DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->default(now()->startOfMonth())
                    ->visible(fn (callable $get) => $get('tipe_laporan') === 'custom'),
                    
                DatePicker::make('tanggal_akhir')
                    ->label('Tanggal Akhir')
                    ->required()
                    ->default(now())
                    ->visible(fn (callable $get) => $get('tipe_laporan') === 'custom'),
                    
                Select::make('bulan')
                    ->label('Bulan')
                    ->options([
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                        4 => 'April', 5 => 'Mei', 6 => 'Juni',
                        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ])
                    ->default(fn() => now()->month)
                    ->visible(fn (callable $get) => $get('tipe_laporan') === 'monthly'),
                    
                Select::make('triwulan')
                    ->label('Triwulan')
                    ->options([
                        1 => 'Triwulan I (Jan-Mar)',
                        2 => 'Triwulan II (Apr-Jun)',
                        3 => 'Triwulan III (Jul-Sep)',
                        4 => 'Triwulan IV (Okt-Des)',
                    ])
                    ->default(fn() => ceil(now()->month / 3))
                    ->visible(fn (callable $get) => $get('tipe_laporan') === 'quarterly'),
            ]);
    }

    public function toggleViewMode(): void
    {
        $this->showDetailedView = !$this->showDetailedView;
    }

    public function generateReport(): void
    {
        try {
            $data = $this->form->getState();
            
            // Tentukan rentang tanggal
            $dateRange = $this->getDatesFromFilter($data);
            $tanggalMulai = $dateRange['tanggal_mulai'];
            $tanggalAkhir = $dateRange['tanggal_akhir'];
            
            $tahunAnggaran = TahunAnggaran::findOrFail($data['tahun_anggaran_id']);
            
            // Log proses untuk debugging
            Log::info('Memulai generateReport', [
                'tahun_anggaran' => $tahunAnggaran->tahun,
                'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
                'tanggal_akhir' => $tanggalAkhir->format('Y-m-d')
            ]);
            
            $this->reportData = $this->getReportData($tahunAnggaran, $tanggalMulai, $tanggalAkhir);
            $this->tanggalLaporan = $dateRange['label'];
            $this->judulLaporan = "Realisasi Penerimaan Pajak Daerah " . $dateRange['label'];
            
            // Log hasil untuk debugging
            Log::info('Hasil generateReport', [
                'judul' => $this->judulLaporan,
                'data_count' => $this->reportData ? $this->reportData->count() : 0
            ]);
            
            Notification::make()
                ->title('Laporan berhasil dibuat')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Log::error('Error pada generateReport: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            Notification::make()
                ->title('Terjadi kesalahan')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }
    
    private function getDatesFromFilter($formData)
    {
        $tahunAnggaranId = $formData['tahun_anggaran_id'];
        $tahunAnggaran = TahunAnggaran::find($tahunAnggaranId);
        $tahun = $tahunAnggaran->tahun;
        
        $tipe = $formData['tipe_laporan'];
        
        switch ($tipe) {
            case 'custom':
                return [
                    'tanggal_mulai' => Carbon::parse($formData['tanggal_mulai']),
                    'tanggal_akhir' => Carbon::parse($formData['tanggal_akhir']),
                    'label' => 'Periode ' . Carbon::parse($formData['tanggal_mulai'])->format('d M Y') . 
                               ' s/d ' . Carbon::parse($formData['tanggal_akhir'])->format('d M Y')
                ];
                
            case 'monthly':
                $bulan = $formData['bulan'];
                $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
                
                $namaBulan = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                    4 => 'April', 5 => 'Mei', 6 => 'Juni',
                    7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                    10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                
                return [
                    'tanggal_mulai' => $startDate,
                    'tanggal_akhir' => $endDate,
                    'label' => 'Bulan ' . $namaBulan[$bulan] . ' ' . $tahun
                ];
                
            case 'quarterly':
                $triwulan = $formData['triwulan'];
                $startMonth = (($triwulan - 1) * 3) + 1;
                $endMonth = $triwulan * 3;
                
                $startDate = Carbon::createFromDate($tahun, $startMonth, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($tahun, $endMonth, 1)->endOfMonth();
                
                return [
                    'tanggal_mulai' => $startDate,
                    'tanggal_akhir' => $endDate,
                    'label' => 'Triwulan ' . $triwulan . ' ' . $tahun
                ];
                
            case 'yearly':
                return [
                    'tanggal_mulai' => Carbon::createFromDate($tahun, 1, 1)->startOfYear(),
                    'tanggal_akhir' => Carbon::createFromDate($tahun, 12, 31)->endOfYear(),
                    'label' => 'Tahun ' . $tahun
                ];
                
            default:
               return [
                    'tanggal_mulai' => Carbon::now()->startOfMonth(),
                    'tanggal_akhir' => Carbon::now(),
                    'label' => 'Periode Default'
                ];
        }
    }
    
    private function getReportData(TahunAnggaran $tahunAnggaran, $tanggalMulai, $tanggalAkhir): Collection
    {
        $result = collect();
        
        try {
            // Ambil semua kode rekening level 1 (parent)
            $rootNodes = KodeRekening::where('level', 1)
                ->where('tahun_anggaran_id', $tahunAnggaran->id)
                ->orderBy('kode')
                ->get();
            
            Log::info('Root nodes', ['count' => $rootNodes->count()]);
            
            foreach ($rootNodes as $node) {
                $nodeData = $this->processNode($node, $tahunAnggaran, $tanggalMulai, $tanggalAkhir);
                $result->push($nodeData);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Error pada getReportData: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }
    
    private function processNode(KodeRekening $node, TahunAnggaran $tahunAnggaran, $tanggalMulai, $tanggalAkhir, $depth = 0): array
    {
        try {
            // Data dasar
            $nodeData = [
                'id' => $node->id,
                'kode' => $node->kode,
                'uraian' => $node->nama,
                'is_pajak' => $node->level == 4, // Pajak adalah level 4 
                'level' => $node->level,
                'depth' => $depth,
                'has_children' => $node->children->isNotEmpty(),
                'pagu_anggaran' => $node->target,
                'target_percentage' => 0,
                'nilai_target' => 0,
                'total_penerimaan' => 0,
                'kurang_target' => 0,
                'persentase_penerimaan' => 0,
                'bulan' => [],
                'children' => []
            ];
            
            // Hitung target kumulatif sampai bulan saat ini
            $currentMonth = Carbon::now()->month;
            $targetPercentage = 100; // Default ke 100%
            
            // Ubah nilai target berdasarkan persentase
            $nodeData['target_percentage'] = $targetPercentage;
            $nodeData['nilai_target'] = ($nodeData['pagu_anggaran'] * $targetPercentage) / 100;
            
            // Jika ini adalah jenis pajak (level 4)
            if ($node->level == 4) {
                // Hitung penerimaan dalam rentang tanggal
                $penerimaan = Penerimaan::where('kode_rekening_id', $node->id)
                    ->whereBetween('tanggal_penerimaan', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
                    ->sum('jumlah');
                    
                $nodeData['total_penerimaan'] = $penerimaan;
                $nodeData['kurang_target'] = $nodeData['nilai_target'] - $penerimaan;
                $nodeData['persentase_penerimaan'] = $nodeData['pagu_anggaran'] > 0 
                    ? ($penerimaan / $nodeData['pagu_anggaran']) * 100 
                    : 0;
                
                // Hitung penerimaan per bulan
                $bulanData = [];
                for ($i = 1; $i <= 12; $i++) {
                    $year = $tahunAnggaran->tahun;
                    $startDate = Carbon::createFromDate($year, $i, 1)->startOfMonth();
                    $endDate = Carbon::createFromDate($year, $i, 1)->endOfMonth();
                    
                    // Pastikan masih dalam rentang tanggal laporan
                    $includeBulan = ($startDate->between($tanggalMulai, $tanggalAkhir) || 
                                     $endDate->between($tanggalMulai, $tanggalAkhir) ||
                                     ($tanggalMulai->between($startDate, $endDate) && 
                                      $tanggalAkhir->between($startDate, $endDate)));
                                        
                    if ($includeBulan) {
                        $penerimaanBulan = Penerimaan::where('kode_rekening_id', $node->id)
                            ->whereBetween('tanggal_penerimaan', [
                                max($startDate->format('Y-m-d'), $tanggalMulai->format('Y-m-d')), 
                                min($endDate->format('Y-m-d'), $tanggalAkhir->format('Y-m-d'))
                            ])
                            ->sum('jumlah');
                            
                        $bulanData[$i] = $penerimaanBulan;
                    } else {
                        $bulanData[$i] = 0;
                    }
                }
                $nodeData['bulan'] = $bulanData;
            } else {
                // Proses anak-anak node
                if ($node->children->isNotEmpty()) {
                    $childrenData = collect();
                    
                    foreach ($node->children()->orderBy('kode')->get() as $child) {
                        $childData = $this->processNode($child, $tahunAnggaran, $tanggalMulai, $tanggalAkhir, $depth + 1);
                        $childrenData->push($childData);
                        
                        // Akumulasi nilai ke parent
                        $nodeData['pagu_anggaran'] += $childData['pagu_anggaran'];
                        $nodeData['nilai_target'] += $childData['nilai_target'];
                        $nodeData['total_penerimaan'] += $childData['total_penerimaan'];
                        
                        // Akumulasi nilai per bulan
                        foreach ($childData['bulan'] as $bulan => $nilai) {
                            if (!isset($nodeData['bulan'][$bulan])) {
                                $nodeData['bulan'][$bulan] = 0;
                            }
                            $nodeData['bulan'][$bulan] += $nilai;
                        }
                    }
                    
                    $nodeData['children'] = $childrenData->toArray();
                    $nodeData['kurang_target'] = $nodeData['nilai_target'] - $nodeData['total_penerimaan'];
                    $nodeData['persentase_penerimaan'] = $nodeData['pagu_anggaran'] > 0 
                        ? ($nodeData['total_penerimaan'] / $nodeData['pagu_anggaran']) * 100 
                        : 0;
                }
            }
            
            return $nodeData;
        } catch (\Exception $e) {
            Log::error('Error pada processNode: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }
    
    public function exportPDF()
    {
        try {
            if (!$this->reportData) {
                Notification::make()
                    ->title('Silahkan generate laporan terlebih dahulu')
                    ->warning()
                    ->send();
                return null;
            }
            
            $formState = $this->form->getState();
            $tahunAnggaran = TahunAnggaran::find($formState['tahun_anggaran_id']);
            
            $data = [
                'reportData' => $this->reportData,
                'tanggalLaporan' => $this->tanggalLaporan,
                'judulLaporan' => $this->judulLaporan,
                'tahun' => $tahunAnggaran ? $tahunAnggaran->tahun : date('Y'),
                'showDetailedView' => $this->showDetailedView,
                'tanggalCetak' => Carbon::now()->format('d-m-Y H:i:s')
            ];
            
            Log::info('Memulai export PDF', [
                'reportData_count' => $this->reportData ? count($this->reportData) : 0,
                'tanggalLaporan' => $this->tanggalLaporan,
                'judulLaporan' => $this->judulLaporan,
            ]);
            
            // Debug data untuk keperluan troubleshooting
            $this->debugPdfData($data);
            
            // Tentukan view berdasarkan detail view
            $view = $this->showDetailedView 
                ? 'exports.laporan-penerimaan-pajak-detail'
                : 'exports.laporan-penerimaan-pajak-summary';
            
            // Buat PDF secara manual dan simpan ke file
            $outputDir = storage_path('app/public/pdf');
            if (!file_exists($outputDir)) {
                mkdir($outputDir, 0755, true);
            }
            
            $outputPath = $outputDir . '/laporan-penerimaan-pajak.pdf';
            
            // Load file, simpan ke PDF, lalu kembalikan response download
            $pdf = PDF::loadView($view, $data);
            $pdf->setPaper('a3', 'landscape');
            $pdf->save($outputPath);
            
            Log::info('PDF berhasil dibuat', ['path' => $outputPath]);
            
            return response()->download($outputPath, 'laporan-penerimaan-pajak.pdf', [
                'Content-Type' => 'application/pdf'
            ])->deleteFileAfterSend();
        } catch (\Exception $e) {
            Log::error('Error pada exportPDF: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            Notification::make()
                ->title('Gagal membuat PDF')
                ->danger()
                ->body($e->getMessage())
                ->send();
                
            return null;
        }
    }
    
    // Method untuk debug data PDF
    private function debugPdfData($data)
    {
        try {
            // Simpan sample data (tanpa reportData yang besar) ke log
            $sampleData = array_merge($data, [
                'reportData' => 'Data too large, removed for logging',
                'reportData_count' => count($data['reportData'] ?? []),
                'sample_item' => count($data['reportData'] ?? []) > 0 ? $data['reportData'][0] : null
            ]);
            
            Log::info('PDF Data', $sampleData);
            
            // Simpan data ke file untuk debugging eksternal
            $debugData = array_merge($data, [
                'reportData' => json_decode(json_encode($data['reportData'] ?? []), true)
            ]);
            
            file_put_contents(
                storage_path('app/pdf-debug.json'), 
                json_encode($debugData, JSON_PRETTY_PRINT)
            );
            
            return true;
        } catch (\Exception $e) {
            Log::error('Debug data error: ' . $e->getMessage());
            return false;
        }
    }
    
    public function getTargetPercentageLabel(): string
    {
        $currentDate = Carbon::now();
        $currentMonth = $currentDate->month;
        
        $tahunAnggaranId = $this->form->getState()['tahun_anggaran_id'] ?? null;
        if (!$tahunAnggaranId) {
            return "Target 0%";
        }
        
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return "Target 100% (s/d {$namaBulan[$currentMonth]})";
    }
    
    public function render(): View
    {
        return view('filament.pages.laporan-penerimaan-pajak');
    }
}