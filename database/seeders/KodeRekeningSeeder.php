<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\KodeRekening;
use Illuminate\Database\Seeder;

class KodeRekeningSeeder extends Seeder
{
    public function run(): void
    {
        // Level 1
        $pendapatanDaerah = KodeRekening::create([
            'kode' => '4',
            'uraian' => 'PENDAPATAN DAERAH',
            'level' => 1,
            'is_pajak' => false,
        ]);
        
        // Level 2
        $pad = KodeRekening::create([
            'kode' => '4.1',
            'uraian' => 'PENDAPATAN ASLI DAERAH',
            'parent_id' => $pendapatanDaerah->id,
            'level' => 2,
            'is_pajak' => false,
        ]);
        
        $danaPerimbangan = KodeRekening::create([
            'kode' => '4.2',
            'uraian' => 'DANA PERIMBANGAN',
            'parent_id' => $pendapatanDaerah->id,
            'level' => 2,
            'is_pajak' => false,
        ]);
        
        $lainLain = KodeRekening::create([
            'kode' => '4.3',
            'uraian' => 'LAIN-LAIN PENDAPATAN DAERAH YANG SAH',
            'parent_id' => $pendapatanDaerah->id,
            'level' => 2,
            'is_pajak' => false,
        ]);
        
        // Level 3
        $pajakDaerah = KodeRekening::create([
            'kode' => '4.1.1',
            'uraian' => 'PAJAK DAERAH',
            'parent_id' => $pad->id,
            'level' => 3,
            'is_pajak' => false,
        ]);
        
        $retribusiDaerah = KodeRekening::create([
            'kode' => '4.1.2',
            'uraian' => 'RETRIBUSI DAERAH',
            'parent_id' => $pad->id,
            'level' => 3,
            'is_pajak' => false,
        ]);
        
        $hasilPengelolaanKekayaan = KodeRekening::create([
            'kode' => '4.1.3',
            'uraian' => 'HASIL PENGELOLAAN KEKAYAAN DAERAH YANG DIPISAHKAN',
            'parent_id' => $pad->id,
            'level' => 3,
            'is_pajak' => false,
        ]);
        
        $lainLainPad = KodeRekening::create([
            'kode' => '4.1.4',
            'uraian' => 'LAIN-LAIN PAD YANG SAH',
            'parent_id' => $pad->id,
            'level' => 3,
            'is_pajak' => false,
        ]);
        
        // Level 4 (Jenis Pajak)
        KodeRekening::create([
            'kode' => '4.1.1.01',
            'uraian' => 'Pajak Hotel',
            'parent_id' => $pajakDaerah->id,
            'level' => 4,
            'is_pajak' => true,
        ]);
        
        KodeRekening::create([
            'kode' => '4.1.1.02',
            'uraian' => 'Pajak Restoran',
            'parent_id' => $pajakDaerah->id,
            'level' => 4,
            'is_pajak' => true,
        ]);
        
        KodeRekening::create([
            'kode' => '4.1.1.03',
            'uraian' => 'Pajak Hiburan',
            'parent_id' => $pajakDaerah->id,
            'level' => 4,
            'is_pajak' => true,
        ]);
        
        KodeRekening::create([
            'kode' => '4.1.1.04',
            'uraian' => 'Pajak Reklame',
            'parent_id' => $pajakDaerah->id,
            'level' => 4,
            'is_pajak' => true,
        ]);
        
        KodeRekening::create([
            'kode' => '4.1.1.05',
            'uraian' => 'Pajak Penerangan Jalan',
            'parent_id' => $pajakDaerah->id,
            'level' => 4,
            'is_pajak' => true,
        ]);
        
        KodeRekening::create([
            'kode' => '4.1.1.06',
            'uraian' => 'Pajak Mineral Bukan Logam dan Batuan',
            'parent_id' => $pajakDaerah->id,
            'level' => 4,
            'is_pajak' => true,
        ]);
        
        KodeRekening::create([
            'kode' => '4.1.1.07',
            'uraian' => 'Pajak Parkir',
            'parent_id' => $pajakDaerah->id,
            'level' => 4,
            'is_pajak' => true,
        ]);
        
        KodeRekening::create([
            'kode' => '4.1.1.08',
            'uraian' => 'Pajak Air Tanah',
            'parent_id' => $pajakDaerah->id,
            'level' => 4,
            'is_pajak' => true,
        ]);
        
        KodeRekening::create([
            'kode' => '4.1.1.09',
            'uraian' => 'Pajak Sarang Burung Walet',
            'parent_id' => $pajakDaerah->id,
            'level' => 4,
            'is_pajak' => true,
        ]);
        
        KodeRekening::create([
            'kode' => '4.1.1.10',
            'uraian' => 'Pajak Bumi dan Bangunan Perdesaan dan Perkotaan',
            'parent_id' => $pajakDaerah->id,
            'level' => 4,
            'is_pajak' => true,
        ]);
        
        KodeRekening::create([
            'kode' => '4.1.1.11',
            'uraian' => 'Bea Perolehan Hak atas Tanah dan Bangunan',
            'parent_id' => $pajakDaerah->id,
            'level' => 4,
            'is_pajak' => true,
        ]);
    }
}
