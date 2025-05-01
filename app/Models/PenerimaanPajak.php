<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenerimaanPajak extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'target_pajak_id',
        'tanggal_penerimaan',
        'nilai_penerimaan',
        'keterangan',
    ];
    
    protected $casts = [
        'tanggal_penerimaan' => 'date',
    ];
    
    public function targetPajak()
    {
        return $this->belongsTo(TargetPajak::class);
    }
    
    public function getMingguAttribute()
    {
        return (int)ceil($this->tanggal_penerimaan->day / 7);
    }
    
    public function getBulanAttribute()
    {
        return $this->tanggal_penerimaan->month;
    }
    
    public function getTriwulanAttribute()
    {
        return ceil($this->bulan / 3);
    }
}
