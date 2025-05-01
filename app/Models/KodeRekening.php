<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KodeRekening extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode',
        'uraian',
        'parent_id',
        'level',
        'is_pajak',
    ];
    
    protected $casts = [
        'is_pajak' => 'boolean',
    ];
    
    public function parent()
    {
        return $this->belongsTo(KodeRekening::class, 'parent_id');
    }
    
    public function children()
    {
        return $this->hasMany(KodeRekening::class, 'parent_id');
    }
    
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }
    
    public function targetPajaks()
    {
        return $this->hasMany(TargetPajak::class);
    }
    
    public function getLevelFourDescendantsAttribute()
    {
        // Rekursif mencari semua kode rekening level 4 yang merupakan turunan
        $result = [];
        
        if ($this->level == 4 && $this->is_pajak) {
            $result[] = $this->id;
        } else {
            foreach ($this->children as $child) {
                $result = array_merge($result, $child->level_four_descendants);
            }
        }
        
        return $result;
    }
    
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
    
    public function scopeLevel($query, $level)
    {
        return $query->where('level', $level);
    }
}
