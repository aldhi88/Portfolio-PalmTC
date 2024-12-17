<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcRootingObDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }
    public function tc_rooting_obs(){
        return $this->belongsTo(TcRootingOb::class,'tc_rooting_ob_id');
    }
    public function tc_rooting_bottles(){
        return $this->belongsTo(TcRootingBottle::class,'tc_rooting_bottle_id');
    }

    // process
    public static function firstTotalLeaf($initId,$obsId,$bottleId){
        $stokAwal = TcRootingBottle::where('id',$bottleId)->first()->getAttribute('leaf_count');
        $q = TcRootingObDetail::where('tc_init_id',$initId)
            ->where('tc_rooting_bottle_id',$bottleId)
            ->where('tc_rooting_ob_id','<',$obsId)
            ->orderBy('tc_rooting_ob_id','desc')
            ->get();
        if(count($q)==0){
            $return = $stokAwal;
        }else{
            $dt = collect($q->toArray());
            $usedStok = $dt->sum('leaf_oxidate') + $dt->sum('leaf_contam') + $dt->sum('leaf_other');
            // transfer back
            $backBottle = 0;
            $return = $stokAwal - $usedStok + $backBottle;
        }
        return $return;
    }
    public static function firstTotal($initId,$obsId,$bottleId){
        $stokAwal = TcRootingBottle::where('id',$bottleId)->first()->getAttribute('bottle_count');
        $q = TcRootingObDetail::where('tc_init_id',$initId)
            ->where('tc_rooting_bottle_id',$bottleId)
            ->where('tc_rooting_ob_id','<',$obsId)
            ->orderBy('tc_rooting_ob_id','desc')
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
        $stokAwal = TcRootingBottle::where('id',$bottleId)->first()->getAttribute('bottle_count');
        $q = TcRootingObDetail::where('tc_init_id',$initId)
            ->where('tc_rooting_bottle_id',$bottleId)
            ->where('tc_rooting_ob_id','<=',$obsId)
            ->orderBy('tc_rooting_ob_id','desc')
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
    public static function lastTotalLeaf($initId,$obsId,$bottleId){
        $stokAwal = TcRootingBottle::where('id',$bottleId)->first()->getAttribute('leaf_count');
        $q = TcRootingObDetail::where('tc_init_id',$initId)->where('tc_rooting_bottle_id',$bottleId)
            ->where('tc_rooting_ob_id','<=',$obsId)->orderBy('tc_rooting_ob_id','desc')->get();
        if(count($q)==0){
            $return = $stokAwal;
        }else{
            $dt = collect($q->toArray());
            $usedStok = $dt->sum('leaf_oxidate') + $dt->sum('leaf_contam') + $dt->sum('leaf_other');
            // transfer back
            $backBottle = 0;
            $return = $stokAwal - $usedStok + $backBottle;
        }
        return $return;
    }
}
