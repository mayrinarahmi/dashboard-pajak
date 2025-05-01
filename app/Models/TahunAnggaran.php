<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class TahunAnggaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tahun',
        'tanggal_mulai',
        'tanggal_selesai',
        'aktif'
    ];
    
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'aktif' => 'boolean',
    ];
    
    public function targetPajaks()
    {
        return $this->hasMany(TargetPajak::class);
    }
    
    public function targetKelompoks()
    {
        return $this->hasMany(TargetKelompok::class);
    }
    
    public function getTargetForBulan($bulan)
    {
        // Cari kelompok yang berisi bulan ini
        $kelompok = $this->targetKelompoks()
            ->whereHas('bulanTerkaits', function ($query) use ($bulan) {
                $query->where('bulan', $bulan);
            })
            ->first();
            
        return $kelompok ? $kelompok->persentase_target : 0;
    }
    
    public function getTargetKumulatifSampai($bulan)
    {
        $bulanTercakup = [];
        for ($i = 1; $i <= $bulan; $i++) {
            $bulanTercakup[] = $i;
        }
        
        // Hitung total persentase dari kelompok yang bulannya tercakup dalam range
        $totalPersentase = 0;
        
        $kelompoks = $this->targetKelompoks;
        
        foreach ($kelompoks as $kelompok) {
            $bulanKelompok = $kelompok->bulanTerkaits->pluck('bulan')->toArray();
            
            // Jika semua bulan dalam kelompok ada dalam bulan tercakup
            if (!array_diff($bulanKelompok, $bulanTercakup)) {
                $totalPersentase += $kelompok->persentase_target;
            }
        }
        
        return $totalPersentase;
    }

}
