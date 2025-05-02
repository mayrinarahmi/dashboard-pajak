<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penerimaan Pajak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Laporan Penerimaan Pajak Daerah</h1>
        
        <div class="card">
            <div class="card-body">
                <form action="{{ route('laporan.standar.generate') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="tahun_anggaran_id" class="form-label">Tahun Anggaran</label>
                        <select class="form-select" name="tahun_anggaran_id" required>
                            <option value="">-- Pilih Tahun Anggaran --</option>
                            @foreach($tahunAnggaran as $id => $tahun)
                                <option value="{{ $id }}">{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="tanggal_mulai" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" name="tanggal_akhir" required>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Generate Laporan</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>