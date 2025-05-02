<!DOCTYPE html>
<html>
<head>
    <title>Hasil Laporan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-2">LAPORAN PENERIMAAN PAJAK DAERAH</h1>
        <h3 class="mb-3">TAHUN ANGGARAN {{ $tahunAnggaranLabel }}</h3>
        <p class="mb-4">{{ $periodeLabel }}</p>
        
        <div class="mb-3">
            <a href="{{ route('laporan.standar.index') }}" class="btn btn-secondary me-2">Kembali</a>
            <a href="{{ route('laporan.standar.pdf', [
                'tahun_anggaran_id' => $tahunAnggaran_id,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_akhir' => $tanggalAkhir
            ]) }}" class="btn btn-danger" target="_blank">Download PDF</a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Kode</th>
                        <th>Uraian</th>
                        <th class="text-end">Target</th>
                        <th class="text-end">Realisasi</th>
                        <th class="text-end">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                        <tr class="{{ $item['level'] == 1 ? 'fw-bold bg-light' : '' }}">
                            <td>{{ $item['kode'] }}</td>
                            <td style="padding-left: {{ $item['indent'] * 20 + 8 }}px;">{{ $item['nama'] }}</td>
                            <td class="text-end">{{ number_format($item['target'], 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($item['realisasi'], 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($item['persentase'], 2, ',', '.') }}%</td>
                        </tr>
                    @endforeach
                    <tr class="table-secondary fw-bold">
                        <td colspan="2">TOTAL</td>
                        <td class="text-end">{{ number_format($totalTarget, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($totalRealisasi, 0, ',', '.') }}</td>
                        <td class="text-end">{{ $totalTarget > 0 ? number_format(($totalRealisasi / $totalTarget) * 100, 2, ',', '.') : '0,00' }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>