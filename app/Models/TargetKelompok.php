<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetKelompok extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_anggaran_id',
        'nama_kelompok',
        'persentase_target',
        'keterangan',
    ];
    
    protected $casts = [
        'persentase_target' => 'decimal:2',
    ];
    
    public function tahunAnggaran()
    {
        return $this->belongsTo(TahunAnggaran::class);
    }
    
    public function bulanTerkaits()
    {
        return $this->hasMany(BulanTerkait::class);
    }
    
    public function getBulanListAttribute()
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $bulanIds = $this->bulanTerkaits->pluck('bulan')->toArray();
        $bulanNames = array_map(function($bulanId) use ($namaBulan) {
            return $namaBulan[$bulanId] ?? '';
        }, $bulanIds);
        
        return implode(', ', $bulanNames);
    }
}
