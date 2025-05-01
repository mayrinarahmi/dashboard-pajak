<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulanTerkait extends Model
{
    use HasFactory;

    protected $fillable = [
        'target_kelompok_id',
        'bulan',
    ];
    
    public function targetKelompok()
    {
        return $this->belongsTo(TargetKelompok::class);
    }
    
    public function getNamaBulanAttribute()
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $namaBulan[$this->bulan] ?? '';
    }
    
    public function getTriwulanAttribute()
    {
        return ceil($this->bulan / 3);
    }
}
