<!-- resources/views/exports/laporan-penerimaan-pajak-summary.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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
        .title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 12px;
            margin-bottom: 5px;
        }
        .periode {
            font-size: 10px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 8px;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px;
            vertical-align: middle;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .level-1 {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        .level-2 {
            font-weight: bold;
        }
        .indent-1 { padding-left: 10px; }
        .indent-2 { padding-left: 20px; }
        .indent-3 { padding-left: 30px; }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
        }
        .signature {
            margin-top: 40px;
            text-align: right;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">LAPORAN PENERIMAAN PAJAK DAERAH</div>
        <div class="subtitle">TAHUN ANGGARAN {{ $tahun }}</div>
        <div class="periode">{{ $tanggalLaporan }}</div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="10%">Kode</th>
                <th width="30%">Uraian</th>
                <th width="6%">%</th>
                <th width="12%">Pagu Anggaran</th>
                <th width="12%">Target</th>
                <th width="12%">Realisasi</th>
                <th width="6%">Tw I</th>
                <th width="6%">Tw II</th>
                <th width="6%">Tw III</th>
                <th width="6%">Tw IV</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $node)
                @php
                    $level = $node['level'];
                    $indent = $node['depth'];
                    $paddingLeft = $indent * 5;
                    $class = $level === 1 ? 'level-1' : ($level === 2 ? 'level-2' : '');
                @endphp
                
                <tr class="{{ $class }}">
                    <td>{{ $node['kode'] }}</td>
                    <td style="padding-left: {{ $paddingLeft }}px;">{{ $node['uraian'] }}</td>
                    <td class="text-right">{{ number_format($node['persentase_penerimaan'], 2, ',', '.') }}%</td>
                    <td class="text-right">{{ number_format($node['pagu_anggaran'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($node['nilai_target'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($node['total_penerimaan'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format(($node['bulan'][1] ?? 0) + ($node['bulan'][2] ?? 0) + ($node['bulan'][3] ?? 0), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format(($node['bulan'][4] ?? 0) + ($node['bulan'][5] ?? 0) + ($node['bulan'][6] ?? 0), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format(($node['bulan'][7] ?? 0) + ($node['bulan'][8] ?? 0) + ($node['bulan'][9] ?? 0), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format(($node['bulan'][10] ?? 0) + ($node['bulan'][11] ?? 0) + ($node['bulan'][12] ?? 0), 0, ',', '.') }}</td>
                </tr>
                
                @if(isset($node['children']) && !empty($node['children']))
                    @foreach($node['children'] as $child)
                        @php
                            $childLevel = $child['level']; 
                            $childIndent = $child['depth'];
                            $childPaddingLeft = $childIndent * 5;
                            $childClass = $childLevel === 1 ? 'level-1' : ($childLevel === 2 ? 'level-2' : '');
                        @endphp
                        
                        <tr class="{{ $childClass }}">
                            <td>{{ $child['kode'] }}</td>
                            <td style="padding-left: {{ $childPaddingLeft }}px;">{{ $child['uraian'] }}</td>
                            <td class="text-right">{{ number_format($child['persentase_penerimaan'], 2, ',', '.') }}%</td>
                            <td class="text-right">{{ number_format($child['pagu_anggaran'], 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($child['nilai_target'], 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($child['total_penerimaan'], 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format(($child['bulan'][1] ?? 0) + ($child['bulan'][2] ?? 0) + ($child['bulan'][3] ?? 0), 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format(($child['bulan'][4] ?? 0) + ($child['bulan'][5] ?? 0) + ($child['bulan'][6] ?? 0), 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format(($child['bulan'][7] ?? 0) + ($child['bulan'][8] ?? 0) + ($child['bulan'][9] ?? 0), 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format(($child['bulan'][10] ?? 0) + ($child['bulan'][11] ?? 0) + ($child['bulan'][12] ?? 0), 0, ',', '.') }}</td>
                        </tr>
                        
                        @if(isset($child['children']) && !empty($child['children']))
                            @foreach($child['children'] as $subchild)
                                @php
                                    $subchildIndent = $subchild['depth'];
                                    $subchildPaddingLeft = $subchildIndent * 5;
                                @endphp
                                
                                <tr>
                                    <td>{{ $subchild['kode'] }}</td>
                                    <td style="padding-left: {{ $subchildPaddingLeft }}px;">{{ $subchild['uraian'] }}</td>
                                    <td class="text-right">{{ number_format($subchild['persentase_penerimaan'], 2, ',', '.') }}%</td>
                                    <td class="text-right">{{ number_format($subchild['pagu_anggaran'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($subchild['nilai_target'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($subchild['total_penerimaan'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format(($subchild['bulan'][1] ?? 0) + ($subchild['bulan'][2] ?? 0) + ($subchild['bulan'][3] ?? 0), 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format(($subchild['bulan'][4] ?? 0) + ($subchild['bulan'][5] ?? 0) + ($subchild['bulan'][6] ?? 0), 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format(($subchild['bulan'][7] ?? 0) + ($subchild['bulan'][8] ?? 0) + ($subchild['bulan'][9] ?? 0), 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format(($subchild['bulan'][10] ?? 0) + ($subchild['bulan'][11] ?? 0) + ($subchild['bulan'][12] ?? 0), 0, ',', '.') }}</td>
                                </tr>
                                
                                @if(isset($subchild['children']) && !empty($subchild['children']))
                                    @foreach($subchild['children'] as $grandchild)
                                        @php
                                            $grandchildIndent = $grandchild['depth'];
                                            $grandchildPaddingLeft = $grandchildIndent * 5;
                                        @endphp
                                        
                                        <tr>
                                            <td>{{ $grandchild['kode'] }}</td>
                                            <td style="padding-left: {{ $grandchildPaddingLeft }}px;">{{ $grandchild['uraian'] }}</td>
                                            <td class="text-right">{{ number_format($grandchild['persentase_penerimaan'], 2, ',', '.') }}%</td>
                                            <td class="text-right">{{ number_format($grandchild['pagu_anggaran'], 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($grandchild['nilai_target'], 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($grandchild['total_penerimaan'], 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format(($grandchild['bulan'][1] ?? 0) + ($grandchild['bulan'][2] ?? 0) + ($grandchild['bulan'][3] ?? 0), 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format(($grandchild['bulan'][4] ?? 0) + ($grandchild['bulan'][5] ?? 0) + ($grandchild['bulan'][6] ?? 0), 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format(($grandchild['bulan'][7] ?? 0) + ($grandchild['bulan'][8] ?? 0) + ($grandchild['bulan'][9] ?? 0), 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format(($grandchild['bulan'][10] ?? 0) + ($grandchild['bulan'][11] ?? 0) + ($grandchild['bulan'][12] ?? 0), 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                @endif
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dicetak pada: {{ $tanggalCetak }}</p>
    </div>
    
    <div class="signature">
        <p>............................................</p>
        <p>Kepala Dinas</p>
        <br><br>
        <p>............................................</p>
        <p>NIP.</p>
    </div>
</body>
</html>