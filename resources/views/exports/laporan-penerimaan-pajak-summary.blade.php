<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judulLaporan }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px;
            vertical-align: middle;
        }
        th {
            background-color: #f0f0f0;
            text-align: center;
            font-weight: bold;
            font-size: 8px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .parent {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .child {
            padding-left: 10px;
        }
        .subchild {
            padding-left: 20px;
        }
        .page-break {
            page-break-after: always;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
        }
        .header h2, .header h3 {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $judulLaporan }}</h2>
        <h3>Tahun Anggaran {{ $tahun }}</h3>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Kode Rekening</th>
                <th>Uraian</th>
                <th>%</th>
                <th>Pagu Anggaran</th>
                <th>Target</th>
                <th>Kurang/Di Target</th>
                <th>Penerimaan</th>
                <th>Triwulan I</th>
                <th>Triwulan II</th>
                <th>Triwulan III</th>
                <th>Triwulan IV</th>
            </tr>
        </thead>
        <tbody>
            @php
                function renderNodePDF($node, $level = 0) {
                    $class = '';
                    if ($level === 0) {
                        $class = 'parent';
                    } elseif ($level === 1) {
                        $class = 'child';
                    } else {
                        $class = 'subchild';
                    }
                    
                    $paddingLeft = $level * 5;
            @endphp
            
            <tr class="{{ $class }}">
                <td style="padding-left: {{ $paddingLeft }}px;">{{ $node['kode'] }}</td>
                <td style="padding-left: {{ $paddingLeft }}px;">{{ $node['uraian'] }}</td>
                <td class="text-right">{{ number_format($node['persentase_penerimaan'], 2, ',', '.') }}%</td>
                <td class="text-right">{{ number_format($node['pagu_anggaran'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($node['nilai_target'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($node['kurang_target'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($node['total_penerimaan'], 0, ',', '.') }}</td>
                
                <td class="text-right">{{ number_format($node['bulan'][1] + $node['bulan'][2] + $node['bulan'][3], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($node['bulan'][4] + $node['bulan'][5] + $node['bulan'][6], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($node['bulan'][7] + $node['bulan'][8] + $node['bulan'][9], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($node['bulan'][10] + $node['bulan'][11] + $node['bulan'][12], 0, ',', '.') }}</td>
            </tr>
            
            @if(isset($node['children']) && count($node['children']) > 0)
                @foreach($node['children'] as $child)
                    @php renderNodePDF($child, $level + 1) @endphp
                @endforeach
            @endif
            
            @php
                }
                
                foreach($reportData as $node) {
                    renderNodePDF($node);
                }
            @endphp
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
        <p>
            <table style="width: 100%; border: none; margin-top: 30px;">
                <tr style="border: none;">
                    <td style="width: 60%; border: none;"></td>
                    <td style="width: 40%; border: none; text-align: center;">
                        <p>........................., {{ date('d F Y') }}</p>
                        <p>KEPALA BIDANG PENDAPATAN</p>
                        <br><br><br>
                        <p style="font-weight: bold; text-decoration: underline;">____________________________</p>
                        <p>NIP. __________________</p>
                    </td>
                </tr>
            </table>
        </p>
    </div>
</body>
</html>
```

#### resources/views/exports/laporan-penerimaan-pajak-detail.blade.php:

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judulLaporan }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px;
            vertical-align: middle;
        }
        th {
            background-color: #f0f0f0;
            text-align: center;
            font-weight: bold;
            font-size: 8px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .parent {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .child {
            padding-left: 10px;
        }
        .subchild {
            padding-left: 20px;
        }
        .page-break {
            page-break-after: always;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
        }
        .header h2, .header h3 {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $judulLaporan }}</h2>
        <h3>Tahun Anggaran {{ $tahun }}</h3>
    </div>
    
    <table>
        <thead>
            <tr>
                <th rowspan="2">Kode Rekening</th>
                <th rowspan="2">Uraian</th>
                <th rowspan="2">%</th>
                <th rowspan="2">Pagu Anggaran</th>
                <th rowspan="2">Target</th>
                <th rowspan="2">Kurang/Di Target</th>
                <th rowspan="2">Penerimaan</th>
                <th colspan="3">Triwulan I</th>
                <th colspan="3">Triwulan II</th>
                <th colspan="3">Triwulan III</th>
                <th colspan="3">Triwulan IV</th>
            </tr>
            <tr>
                <th>Jan</th>
                <th>Feb</th>
                <th>Mar</th>
                <th>Apr</th>
                <th>Mei</th>
                <th>Jun</th>
                <th>Jul</th>
                <th>Ags</th>
                <th>Sep</th>
                <th>Okt</th>
                <th>Nov</th>
                <th>Des</th>
            </tr>
        </thead>
        <tbody>
            @php
                function renderNodePDFDetail($node, $level = 0) {
                    $class = '';
                    if
                    $class = '';
                    if ($level === 0) {
                        $class = 'parent';
                    } elseif ($level === 1) {
                        $class = 'child';
                    } else {
                        $class = 'subchild';
                    }
                    
                    $paddingLeft = $level * 5;
            @endphp
            
            <tr class="{{ $class }}">
                <td style="padding-left: {{ $paddingLeft }}px;">{{ $node['kode'] }}</td>
                <td style="padding-left: {{ $paddingLeft }}px;">{{ $node['uraian'] }}</td>
                <td class="text-right">{{ number_format($node['persentase_penerimaan'], 2, ',', '.') }}%</td>
                <td class="text-right">{{ number_format($node['pagu_anggaran'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($node['nilai_target'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($node['kurang_target'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($node['total_penerimaan'], 0, ',', '.') }}</td>
                
                @for ($i = 1; $i <= 12; $i++)
                    <td class="text-right">{{ number_format($node['bulan'][$i] ?? 0, 0, ',', '.') }}</td>
                @endfor
            </tr>
            
            @if(isset($node['children']) && count($node['children']) > 0)
                @foreach($node['children'] as $child)
                    @php renderNodePDFDetail($child, $level + 1) @endphp
                @endforeach
            @endif
            
            @php
                }
                
                foreach($reportData as $node) {
                    renderNodePDFDetail($node);
                }
            @endphp
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
        <p>
            <table style="width: 100%; border: none; margin-top: 30px;">
                <tr style="border: none;">
                    <td style="width: 60%; border: none;"></td>
                    <td style="width: 40%; border: none; text-align: center;">
                        <p>........................., {{ date('d F Y') }}</p>
                        <p>KEPALA BIDANG PENDAPATAN</p>
                        <br><br><br>
                        <p style="font-weight: bold; text-decoration: underline;">____________________________</p>
                        <p>NIP. __________________</p>
                    </td>
                </tr>
            </table>
        </p>
    </div>
</body>
</html>