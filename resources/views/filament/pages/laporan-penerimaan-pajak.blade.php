<x-filament-panels::page>
<x-filament::section>
        <form wire:submit="generateReport">
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
            </div>
            
            @if(!$showDetailedView)
            <!-- Tampilan Ringkasan (Per Triwulan) -->
            <table class="w-full text-sm text-left border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-2">Kode Rekening</th>
                        <th class="border px-2 py-2">Uraian</th>
                        <th class="border px-2 py-2">%</th>
                        <th class="border px-2 py-2">Pagu Anggaran</th>
                        <th class="border px-2 py-2">{{ $this->getTargetPercentageLabel() }}</th>
                        <th class="border px-2 py-2">Kurang / Di Target</th>
                        <th class="border px-2 py-2">Penerimaan ({{ $tanggalLaporan }})</th>
                        <th class="border px-2 py-2">Triwulan I</th>
                        <th class="border px-2 py-2">Triwulan II</th>
                        <th class="border px-2 py-2">Triwulan III</th>
                        <th class="border px-2 py-2">Triwulan IV</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        function renderNode($node, $level = 0) {
                            $paddingLeft = $level * 10;
                            $bgColor = $level === 0 ? 'bg-gray-200' : ($level === 1 ? 'bg-gray-100' : '');
                            $fontWeight = $level < 2 ? 'font-bold' : 'font-normal';
                    @endphp
                    
                    <tr class="{{ $bgColor }} {{ $fontWeight }}">
                        <td class="border px-2 py-1" style="padding-left: {{ $paddingLeft + 8 }}px;">{{ $node['kode'] }}</td>
                        <td class="border px-2 py-1" style="padding-left: {{ $paddingLeft + 8 }}px;">{{ $node['uraian'] }}</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($node['persentase_penerimaan'], 2, ',', '.') }}%</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($node['pagu_anggaran'], 0, ',', '.') }}</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($node['nilai_target'], 0, ',', '.') }}</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($node['kurang_target'], 0, ',', '.') }}</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($node['total_penerimaan'], 0, ',', '.') }}</td>
                        
                        <td class="border px-2 py-1 text-right">{{ number_format($node['bulan'][1] + $node['bulan'][2] + $node['bulan'][3], 0, ',', '.') }}</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($node['bulan'][4] + $node['bulan'][5] + $node['bulan'][6], 0, ',', '.') }}</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($node['bulan'][7] + $node['bulan'][8] + $node['bulan'][9], 0, ',', '.') }}</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($node['bulan'][10] + $node['bulan'][11] + $node['bulan'][12], 0, ',', '.') }}</td>
                    </tr>
                    
                    @if(isset($node['children']) && count($node['children']) > 0)
                        @foreach($node['children'] as $child)
                            @php renderNode($child, $level + 1) @endphp
                        @endforeach
                    @endif
                    
                    @php
                        }
                        
                        foreach($reportData as $node) {
                            renderNode($node);
                        }
                    @endphp
                </tbody>
            </table>
            @else
            <!-- Tampilan Detail (Per Bulan) -->
            <table class="w-full text-sm text-left border">
                <thead class="bg-gray-100">
                    <tr>
                        <th rowspan="2" class="border px-2 py-2">Kode Rekening</th>
                        <th rowspan="2" class="border px-2 py-2">Uraian</th>
                        <th rowspan="2" class="border px-2 py-2">%</th>
                        <th rowspan="2" class="border px-2 py-2">Pagu Anggaran</th>
                        <th rowspan="2" class="border px-2 py-2">{{ $this->getTargetPercentageLabel() }}</th>
                        <th rowspan="2" class="border px-2 py-2">Kurang / Di Target</th>
                        <th rowspan="2" class="border px-2 py-2">Penerimaan ({{ $tanggalLaporan }})</th>
                        <th colspan="3" class="border px-2 py-2 text-center">Triwulan I</th>
                        <th colspan="3" class="border px-2 py-2 text-center">Triwulan II</th>
                        <th colspan="3" class="border px-2 py-2 text-center">Triwulan III</th>
                        <th colspan="3" class="border px-2 py-2 text-center">Triwulan IV</th>
                    </tr>
                    <tr>
                        <th class="border px-2 py-2">Jan</th>
                        <th class="border px-2 py-2">Feb</th>
                        <th class="border px-2 py-2">Mar</th>
                        <th class="border px-2 py-2">Apr</th>
                        <th class="border px-2 py-2">Mei</th>
                        <th class="border px-2 py-2">Jun</th>
                        <th class="border px-2 py-2">Jul</th>
                        <th class="border px-2 py-2">Ags</th>
                        <th class="border px-2 py-2">Sep</th>
                        <th class="border px-2 py-2">Okt</th>
                        <th class="border px-2 py-2">Nov</th>
                        <th class="border px-2 py-2">Des</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        function renderNodeDetailed($node, $level = 0) {
                            $paddingLeft = $level * 10;
                            $bgColor = $level === 0 ? 'bg-gray-200' : ($level === 1 ? 'bg-gray-100' : '');
                            $fontWeight = $level < 2 ? 'font-bold' : 'font-normal';
                    @endphp
                    
                    <tr class="{{ $bgColor }} {{ $fontWeight }}">
                        <td class="border px-2 py-1" style="padding-left: {{ $paddingLeft + 8 }}px;">{{ $node['kode'] }}</td>
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
                    
                    @if(isset($node['children']) && count($node['children']) > 0)
                        @foreach($node['children'] as $child)
                            @php renderNodeDetailed($child, $level + 1) @endphp
                        @endforeach
                    @endif
                    
                    @php
                        }
                        
                        foreach($reportData as $node) {
                            renderNodeDetailed($node);
                        }
                    @endphp
                </tbody>
            </table>
            @endif
        </div>
    </x-filament::section>
    @endif
</x-filament-panels::page>
