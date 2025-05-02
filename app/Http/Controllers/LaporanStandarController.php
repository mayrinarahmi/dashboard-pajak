<?php

namespace App\Http\Controllers;

use App\Models\KodeRekening;
use App\Models\Penerimaan;
use App\Models\TahunAnggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanStandarController extends Controller
{
    public function index()
    {
        $tahunAnggaran = TahunAnggaran::orderBy('tahun', 'desc')->pluck('tahun', 'id');
        return view('laporan.index', compact('tahunAnggaran'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'tahun_anggaran_id' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);
        
        // Proses data laporan sebagaimana diperlukan
        // ...
        
        // Data demo untuk testing
        $data = [
            ['id' => 1, 'indent' => 0, 'kode' => '4', 'nama' => 'PENDAPATAN DAERAH', 'target' => 1000000000, 'realisasi' => 850000000, 'persentase' => 85, 'level' => 1],
            ['id' => 2, 'indent' => 1, 'kode' => '4.1', 'nama' => 'PENDAPATAN ASLI DAERAH', 'target' => 500000000, 'realisasi' => 425000000, 'persentase' => 85, 'level' => 2],
            ['id' => 3, 'indent' => 2, 'kode' => '4.1.1', 'nama' => 'PAJAK DAERAH', 'target' => 300000000, 'realisasi' => 255000000, 'persentase' => 85, 'level' => 3],
            ['id' => 4, 'indent' => 3, 'kode' => '4.1.1.01', 'nama' => 'Pajak Hotel', 'target' => 100000000, 'realisasi' => 85000000, 'persentase' => 85, 'level' => 4],
            ['id' => 5, 'indent' => 3, 'kode' => '4.1.1.02', 'nama' => 'Pajak Restoran', 'target' => 200000000, 'realisasi' => 170000000, 'persentase' => 85, 'level' => 4],
            ['id' => 6, 'indent' => 1, 'kode' => '4.2', 'nama' => 'DANA PERIMBANGAN', 'target' => 500000000, 'realisasi' => 425000000, 'persentase' => 85, 'level' => 2],
        ];
        
        $periodeLabel = 'Periode: ' . Carbon::parse($request->tanggal_mulai)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
        
        return view('laporan.hasil', [
            'data' => $data,
            'periodeLabel' => $periodeLabel,
            'totalTarget' => 1000000000,
            'totalRealisasi' => 850000000,
            'tahunAnggaranLabel' => TahunAnggaran::find($request->tahun_anggaran_id)->tahun,
            'tanggalMulai' => $request->tanggal_mulai,
            'tanggalAkhir' => $request->tanggal_akhir,
            'tahunAnggaran_id' => $request->tahun_anggaran_id
        ]);
    }
    
    public function generatePdf(Request $request)
    {
        // Data demo untuk test PDF
        $data = [
            ['id' => 1, 'indent' => 0, 'kode' => '4', 'nama' => 'PENDAPATAN DAERAH', 'target' => 1000000000, 'realisasi' => 850000000, 'persentase' => 85, 'level' => 1],
            ['id' => 2, 'indent' => 1, 'kode' => '4.1', 'nama' => 'PENDAPATAN ASLI DAERAH', 'target' => 500000000, 'realisasi' => 425000000, 'persentase' => 85, 'level' => 2],
        ];
        
        $periodeLabel = 'Periode: ' . Carbon::parse($request->tanggal_mulai)->format('d-m-Y') . ' s/d ' . Carbon::parse($request->tanggal_akhir)->format('d-m-Y');
        
        $view = view('laporan.pdf', [
            'data' => $data,
            'periodeLabel' => $periodeLabel,
            'totalTarget' => 1000000000,
            'totalRealisasi' => 850000000,
            'tahunAnggaranLabel' => isset($request->tahun_anggaran_id) ? TahunAnggaran::find($request->tahun_anggaran_id)->tahun : date('Y'),
            'tanggalCetak' => Carbon::now()->format('d-m-Y H:i:s')
        ]);
        
        try {
            $pdf = Pdf::loadHTML($view);
            return $pdf->download('laporan-penerimaan-pajak.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }
}