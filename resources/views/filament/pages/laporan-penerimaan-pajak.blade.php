<x-filament-panels::page>
    <x-filament::section>
        <form wire:submit.prevent="generateReport">
            {{ $this->form }}
            
            <div class="flex items-center justify-between mt-6">
                <x-filament::button type="submit">
                    Generate Laporan
                </x-filament::button>
                
                @if($reportData)
                <div class="flex space-x-2">
                    <x-filament::button 
                        wire:click="toggleViewMode"
                        size="sm"
                    >
                        {{ $showDetailedView ? 'Tampilkan Ringkasan' : 'Tampilkan Detail Bulanan' }}
                    </x-filament::button>
                    
                    <x-filament::button 
                        wire:click="exportPDF"
                        color="success"
                        size="sm"
                    >
                        Download PDF
                    </x-filament::button>
                </div>
                @endif
            </div>
        </form>
    </x-filament::section>
    
    @if($reportData)
    <x-filament::section class="mt-6">
        <div class="overflow-x-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">{{ $judulLaporan }}</h2>
                <p>{{ $tanggalLaporan }}</p>
            </div>
            
            @if(!$showDetailedView)
            <!-- Tampilan Ringkasan (Per Triwulan) -->
            <table class="w-full text-sm text-left border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-2">Kode Rekening</th>
                        <th class="border px-2 py-2">Uraian</th>
                        <th class="border px-2 py-2 text-right">%</th>
                        <th class="border px-2 py-2 text-right">Pagu Anggaran</th>
                        <th class="border px-2 py-2 text-right">{{ $this->getTargetPercentageLabel() }}</th>
                        <th class="border px-2 py-2 text-right">Kurang / Di Target</th>
                        <th class="border px-2 py-2 text-right">Penerimaan ({{ $tanggalLaporan }})</th>
                        <th class="border px-2 py-2 text-right">Triwulan I</th>
                        <th class="border px-2 py-2 text-right">Triwulan II</th>
                        <th class="border px-2 py-2 text-right">Triwulan III</th>
                        <th class="border px-2 py-2 text-right">Triwulan IV</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData as $node)
                        @php
                            $level = $node['level'];
                            $indent = $node['depth'];
                            $paddingLeft = $indent * 10;
                            $bgColor = $level === 1 ? 'bg-gray-200' : ($level === 2 ? 'bg-gray-100' : '');
                            $fontWeight = $level < 3 ? 'font-bold' : 'font-normal';
                        @endphp
                        
                        <tr class="{{ $bgColor }} {{ $fontWeight }}">
                            <td class="border px-2 py-1">{{ $node['kode'] }}</td>
                            <td class="border px-2 py-1" style="padding-left: {{ $paddingLeft + 8 }}px;">{{ $node['uraian'] }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($node['persentase_penerimaan'], 2, ',', '.') }}%</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($node['pagu_anggaran'], 0, ',', '.') }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($node['nilai_target'], 0, ',', '.') }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($node['kurang_target'], 0, ',', '.') }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($node['total_penerimaan'], 0, ',', '.') }}</td>
                            
                            <td class="border px-2 py-1 text-right">{{ number_format($node['bulan'][1] + $node['bulan'][2] + $node['bulan'][3], 0, ',', '.') }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($node['bulan'][4] + $node['bulan'][5] + $node['bulan'][6], 0, ',', '.') }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($node['bulan'][7] + $node['bulan'][8] + $node['bulan'][9], 0, ',', '.') }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($node['bulan'][10] + ($node['bulan'][11] ?? 0) + ($node['bulan'][12] ?? 0), 0, ',', '.') }}</td>
                        </tr>
                        
                        @if(isset($node['children']) && !empty($node['children']))
                            @foreach($node['children'] as $child)
                                @php
                                    $childLevel = $child['level']; 
                                    $childIndent = $child['depth'];
                                    $childPaddingLeft = $childIndent * 10;
                                    $childBgColor = $childLevel === 1 ? 'bg-gray-200' : ($childLevel === 2 ? 'bg-gray-100' : '');
                                    $childFontWeight = $childLevel < 3 ? 'font-bold' : 'font-normal';
                                @endphp
                                
                                <tr class="{{ $childBgColor }} {{ $childFontWeight }}">
                                    <td class="border px-2 py-1">{{ $child['kode'] }}</td>
                                    <td class="border px-2 py-1" style="padding-left: {{ $childPaddingLeft + 8 }}px;">{{ $child['uraian'] }}</td>
                                    <td class="border px-2 py-1 text-right">{{ number_format($child['persentase_penerimaan'], 2, ',', '.') }}%</td>
                                    <td class="border px-2 py-1 text-right">{{ number_format($child['pagu_anggaran'], 0, ',', '.') }}</td>
                                    <td class="border px-2 py-1 text-right">{{ number_format($child['nilai_target'], 0, ',', '.') }}</td>
                                    <td class="border px-2 py-1 text-right">{{ number_format($child['kurang_target'], 0, ',', '.') }}</td>
                                    <td class="border px-2 py-1 text-right">{{ number_format($child['total_penerimaan'], 0, ',', '.') }}</td>
                                    
                                    <td class="border px-2 py-1 text-right">{{ number_format(($child['bulan'][1] ?? 0) + ($child['bulan'][2] ?? 0) + ($child['bulan'][3] ?? 0), 0, ',', '.') }}</td>
                                    <td class="border px-2 py-1 text-right">{{ number_format(($child['bulan'][4] ?? 0) + ($child['bulan'][5] ?? 0) + ($child['bulan'][6] ?? 0), 0, ',', '.') }}</td>
                                    <td class="border px-2 py-1 text-right">{{ number_format(($child['bulan'][7] ?? 0) + ($child['bulan'][8] ?? 0) + ($child['bulan'][9] ?? 0), 0, ',', '.') }}</td>
                                    <td class="border px-2 py-1 text-right">{{ number_format(($child['bulan'][10] ?? 0) + ($child['bulan'][11] ?? 0) + ($child['bulan'][12] ?? 0), 0, ',', '.') }}</td>
                                </tr>
                                
                                @if(isset($child['children']) && !empty($child['children']))
                                    @foreach($child['children'] as $subchild)
                                        @php
                                            $subchildLevel = $subchild['level']; 
                                            $subchildIndent = $subchild['depth'];
                                            $subchildPaddingLeft = $subchildIndent * 10;
                                            $subchildFontWeight = $subchildLevel < 3 ? 'font-bold' : 'font-normal';
                                        @endphp
                                        
                                        <tr class="{{ $subchildFontWeight }}">
                                            <td class="border px-2 py-1">{{ $subchild['kode'] }}</td>
                                            <td class="border px-2 py-1" style="padding-left: {{ $subchildPaddingLeft + 8 }}px;">{{ $subchild['uraian'] }}</td>
                                            <td class="border px-2 py-1 text-right">{{ number_format($subchild['persentase_penerimaan'], 2, ',', '.') }}%</td>
                                            <td class="border px-2 py-1 text-right">{{ number_format($subchild['pagu_anggaran'], 0, ',', '.') }}</td>
                                            <td class="border px-2 py-1 text-right">{{ number_format($subchild['nilai_target'], 0, ',', '.') }}</td>
                                            <td class="border px-2 py-1 text-right">{{ number_format($subchild['kurang_target'], 0, ',', '.') }}</td>
                                            <td class="border px-2 py-1 text-right">{{ number_format($subchild['total_penerimaan'], 0, ',', '.') }}</td>
                                            
                                            <td class="border px-2 py-1 text-right">{{ number_format(($subchild['bulan'][1] ?? 0) + ($subchild['bulan'][2] ?? 0) + ($subchild['bulan'][3] ?? 0), 0, ',', '.') }}</td>
                                            <td class="border px-2 py-1 text-right">{{ number_format(($subchild['bulan'][4] ?? 0) + ($subchild['bulan'][5] ?? 0) + ($subchild['bulan'][6] ?? 0), 0, ',', '.') }}</td>
                                            <td class="border px-2 py-1 text-right">{{ number_format(($subchild['bulan'][7] ?? 0) + ($subchild['bulan'][8] ?? 0) + ($subchild['bulan'][9] ?? 0), 0, ',', '.') }}</td>
                                            <td class="border px-2 py-1 text-right">{{ number_format(($subchild['bulan'][10] ?? 0) + ($subchild['bulan'][11] ?? 0) + ($subchild['bulan'][12] ?? 0), 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
            @else
            <!-- Tampilan Detail (Per Bulan) -->
            <table class="w-full text-sm text-left border">
                <thead class="bg-gray-100">
                    <tr>
                        <th rowspan="2" class="border px-2 py-2">Kode Rekening</th>
                        <th rowspan="2" class="border px-2 py-2">Uraian</th>
                        <th rowspan="2" class="border px-2 py-2 text-right">%</th>
                        <th rowspan="2" class="border px-2 py-2 text-right">Pagu Anggaran</th>
                        <th rowspan="2" class="border px-2 py-2 text-right">{{ $this->getTargetPercentageLabel() }}</th>
                        <th rowspan="2" class="border px-2 py-2 text-right">Kurang / Di Target</th>
                        <th rowspan="2" class="border px-2 py-2 text-right">Penerimaan ({{ $tanggalLaporan }})</th>
                        <th colspan="3" class="border px-2 py-2 text-center">Triwulan I</th>
                        <th colspan="3" class="border px-2 py-2 text-center">Triwulan II</th>
                        <th colspan="3" class="border px-2 py-2 text-center">Triwulan III</th>
                        <th colspan="3" class="border px-2 py-2 text-center">Triwulan IV</th>
                    </tr>
                    <tr>
                        <th class="border px-2 py-2 text-center">Jan</th>
                        <th class="border px-2 py-2 text-center">Feb</th>
                        <th class="border px-2 py-2 text-center">Mar</th>
                        <th class="border px-2 py-2 text-center">Apr</th>
                        <th class="border px-2 py-2 text-center">Mei</th>
                        <th class="border px-2 py-2 text-center">Jun</th>
                        <th class="border px-2 py-2 text-center">Jul</th>
                        <th class="border px-2 py-2 text-center">Ags</th>
                        <th class="border px-2 py-2 text-center">Sep</th>
                        <th class="border px-2 py-2 text-center">Okt</th>
                        <th class="border px-2 py-2 text-center">Nov</th>
                        <th class="border px-2 py-2 text-center">Des</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData as $node)
                        @php
                            $level = $node['level'];
                            $indent = $node['depth'];
                            $paddingLeft = $indent * 10;
                            $bgColor = $level === 1 ? 'bg-gray-200' : ($level === 2 ? 'bg-gray-100' : '');
                            $fontWeight = $level < 3 ? 'font-bold' : 'font-normal';
                        @endphp
                        
                        <tr class="{{ $bgColor }} {{ $fontWeight }}">
                            <td class="border px-2 py-1">{{ $node['kode'] }}</td>
                            <td class="border px-2 py-1" style="padding-left: {{ $paddingLeft + 8 }}px;">{{ $node['uraian'] }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($node['persentase_penerimaan'], 2, ',', '.') }}%</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($node['pagu_anggaran'], 0, ',', '.') }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($node['nilai_target'], 0, ',', '.') }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($node['kurang_target'], 0, ',', '.') }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($node['total_penerimaan'], 0, ',', '.') }}</td>
                            
                            @for ($i = 1; $i <= 12; $i++)
                                <td class="border px-2 py-1 text-right">{{ number_format($node['bulan'][$i] ?? 0, 0, ',', '.') }}</td>
                            @endfor
                        </tr>
                        
                        @if(isset($node['children']) && !empty($node['children']))
                            @foreach($node['children'] as $child)
                                @php
                                    $childLevel = $child['level']; 
                                    $childIndent = $child['depth'];
                                    $childPaddingLeft = $childIndent * 10;
                                    $childBgColor = $childLevel === 1 ? 'bg-gray-200' : ($childLevel === 2 ? 'bg-gray-100' : '');
                                    $childFontWeight = $childLevel < 3 ? 'font-bold' : 'font-normal';
                                @endphp
                                
                                <tr class="{{ $childBgColor }} {{ $childFontWeight }}">
                                    <td class="border px-2 py-1">{{ $child['kode'] }}</td>
                                    <td class="border px-2 py-1" style="padding-left: {{ $childPaddingLeft + 8 }}px;">{{ $child['uraian'] }}</td>
                                    <td class="border px-2 py-1 text-right">{{ number_format($child['persentase_penerimaan'], 2, ',', '.') }}%</td>
                                    <td class="border px-2 py-1 text-right">{{ number_format($child['pagu_anggaran'], 0, ',', '.') }}</td>
                                    <td class="border px-2 py-1 text-right">{{ number_format($child['nilai_target'], 0, ',', '.') }}</td>
                                    <td class="border px-2 py-1 text-right">{{ number_format($child['kurang_target'], 0, ',', '.') }}</td>
                                    <td class="border px-2 py-1 text-right">{{ number_format($child['total_penerimaan'], 0, ',', '.') }}</td>
                                    
                                    @for ($i = 1; $i <= 12; $i++)
                                        <td class="border px-2 py-1 text-right">{{ number_format($child['bulan'][$i] ?? 0, 0, ',', '.') }}</td>
                                    @endfor
                                </tr>
                                
                                @if(isset($child['children']) && !empty($child['children']))
                                    @foreach($child['children'] as $subchild)
                                        @php
                                            $subchildLevel = $subchild['level']; 
                                            $subchildIndent = $subchild['depth'];
                                            $subchildPaddingLeft = $subchildIndent * 10;
                                            $subchildFontWeight = $subchildLevel < 3 ? 'font-bold' : 'font-normal';
                                        @endphp
                                        
                                        <tr class="{{ $subchildFontWeight }}">
                                            <td class="border px-2 py-1">{{ $subchild['kode'] }}</td>
                                            <td class="border px-2 py-1" style="padding-left: {{ $subchildPaddingLeft + 8 }}px;">{{ $subchild['uraian'] }}</td>
                                            <td class="border px-2 py-1 text-right">{{ number_format($subchild['persentase_penerimaan'], 2, ',', '.') }}%</td>
                                            <td class="border px-2 py-1 text-right">{{ number_format($subchild['pagu_anggaran'], 0, ',', '.') }}</td>
                                            <td class="border px-2 py-1 text-right">{{ number_format($subchild['nilai_target'], 0, ',', '.') }}</td>
                                            <td class="border px-2 py-1 text-right">{{ number_format($subchild['kurang_target'], 0, ',', '.') }}</td>
                                            <td class="border px-2 py-1 text-right">{{ number_format($subchild['total_penerimaan'], 0, ',', '.') }}</td>
                                            
                                            @for ($i = 1; $i <= 12; $i++)
                                                <td class="border px-2 py-1 text-right">{{ number_format($subchild['bulan'][$i] ?? 0, 0, ',', '.') }}</td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </x-filament::section>
    @endif
    
</x-filament-panels::page>