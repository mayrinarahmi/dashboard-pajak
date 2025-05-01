<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class TargetPajak extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'tahun_anggaran_id',
        'kode_rekening_id',
        'pagu_anggaran',
    ];
    
    public function tahunAnggaran()
    {
        return $this->belongsTo(TahunAnggaran::class);
    }
    
    public function kodeRekening()
    {
        return $this->belongsTo(KodeRekening::class);
    }
    
    public function penerimaanPajaks()
    {
        return $this->hasMany(PenerimaanPajak::class);
    }
    
    public function getNilaiTargetAttribute()
    {
        $currentDate = Carbon::now();
        $currentMonth = $currentDate->month;
        $targetPercentage = $this->tahunAnggaran->getTargetKumulatifSampai($currentMonth);
        
        return ($this->pagu_anggaran * $targetPercentage) / 100;
    }
    
    public function getNilaiTargetBulanAttribute($bulan)
    {
        $targetPercentage = $this->tahunAnggaran->getTargetForBulan($bulan);
        return ($this->pagu_anggaran * $targetPercentage) / 100;
    }
    
    public function getNilaiTargetKumulatifAttribute($bulan)
    {
        $targetPercentage = $this->tahunAnggaran->getTargetKumulatifSampai($bulan);
        return ($this->pagu_anggaran * $targetPercentage) / 100;
    }
    
    public function getTotalPenerimaanAttribute()
    {
        return $this->penerimaanPajaks->sum('nilai_penerimaan');
    }
    
    public function getKurangTargetAttribute()
    {
        return $this->nilai_target - $this->total_penerimaan;
    }
    
    public function getPersentasePenerimaanAttribute()
    {
        if ($this->pagu_anggaran > 0) {
            return ($this->total_penerimaan / $this->pagu_anggaran) * 100;
        }
        
        return 0;
    }
    
    public function getPenerimaanBulanAttribute($bulan)
    {
        $year = $this->tahunAnggaran->tahun;
        
        $startDate = Carbon::createFromDate($year, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $bulan, 1)->endOfMonth();
        
        return $this->penerimaanPajaks
            ->whereBetween('tanggal_penerimaan', [$startDate, $endDate])
            ->sum('nilai_penerimaan');
    }
    
    public function getPenerimaanDalamRentangAttribute($tanggalMulai, $tanggalAkhir)
    {
        return $this->penerimaanPajaks
            ->whereBetween('tanggal_penerimaan', [$tanggalMulai, $tanggalAkhir])
            ->sum('nilai_penerimaan');
    }
}
