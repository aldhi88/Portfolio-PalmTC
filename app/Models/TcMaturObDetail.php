<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcMaturObDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }
    public function tc_matur_obs(){
        return $this->belongsTo(TcMaturOb::class,'tc_matur_ob_id');
    }
    public function tc_matur_bottles(){
        return $this->belongsTo(TcMaturBottle::class,'tc_matur_bottle_id');
    }

    // process
    public static function firstTotal($initId,$obsId,$bottleId){
        $stokAwal = TcMaturBottle::where('id',$bottleId)->first()->getAttribute('bottle_count');
        $q = TcMaturObDetail::where('tc_init_id',$initId)
            ->where('tc_matur_bottle_id',$bottleId)
            ->where('tc_matur_ob_id','<',$obsId)
            ->orderBy('tc_matur_ob_id','desc')
            ->get();
        if(count($q)==0){
            $return = $stokAwal;
        }else{
            $dt = collect($q->toArray());
            $usedStok = $dt->sum('bottle_oxidate') + $dt->sum('bottle_contam') + $dt->sum('bottle_other');
            // transfer back
            $backBottle = 0;
            $return = $stokAwal - $usedStok + $backBottle;
        }
        return $return;
    }

    public static function lastTotal($initId,$obsId,$bottleId){
        $stokAwal = TcMaturBottle::where('id',$bottleId)->first()->getAttribute('bottle_count');
        $q = TcMaturObDetail::where('tc_init_id',$initId)
            ->where('tc_matur_bottle_id',$bottleId)
            ->where('tc_matur_ob_id','<=',$obsId)
            ->orderBy('tc_matur_ob_id','desc')
            ->get();
        if(count($q)==0){
            $return = $stokAwal;
        }else{
            $dt = collect($q->toArray());
            $usedStok = $dt->sum('bottle_oxidate') + $dt->sum('bottle_contam') + $dt->sum('bottle_other');
            // transfer back
            $backBottle = 0;
            $return = $stokAwal - $usedStok + $backBottle;
        }
        return $return;
    }
}
