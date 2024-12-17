<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TcRootingBottle extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    // relation
    public function tc_bottles(){
        return $this->belongsTo(TcBottle::class,'tc_bottle_id');
    }
    public function tc_workers(){
        return $this->belongsTo(TcWorker::class,'tc_worker_id');
    }
    public function tc_laminars(){
        return $this->belongsTo(TcLaminar::class,'tc_laminar_id');
    }
    public function tc_inits(){
        return $this->belongsTo(TcInit::class,'tc_init_id');
    }

    // process
    public static function firstStock($bottleId){
        $return = TcRootingBottle::where('id',$bottleId)->first()->getAttribute('bottle_count');
        $q = TcRootingTransaction::where('tc_rooting_bottle_id',$bottleId)
            ->orderBy('tc_rooting_ob_id','desc')->get();
        if(count($q) > 0){
            $return = $q->first()->first_total;
        }
        return $return;
    }
    public static function firstStockLeaf($bottleId){
        $return = TcRootingBottle::where('id',$bottleId)->first()->getAttribute('leaf_count');
        $q = TcRootingTransaction::where('tc_rooting_bottle_id',$bottleId)
            ->orderBy('tc_rooting_ob_id','desc')->get();
        if(count($q) > 0){
            $return = $q->first()->first_leaf;
        }
        return $return;
    }
    public static function usedBottleLeaf($bottleId){
        $dt = collect(TcRootingObDetail::where('tc_rooting_bottle_id',$bottleId)->get()->toArray());
        $minBottleOb = $dt->sum('leaf_oxidate') + $dt->sum('leaf_contam') + $dt->sum('leaf_other');
        // transfer back
        $minBottleTransfer = 0;
        $q = TcRootingTransferBottle::where('tc_rooting_bottle_id',$bottleId)->get();
        foreach ($q as $key => $value) {
            $minBottleTransfer += ($value['leaf_rooting']-$value['leaf_left']); 
        }
        
        $return = $minBottleOb + $minBottleTransfer;  
        return $return;
    }
    public static function usedBottle($bottleId){
        $dt = collect(TcRootingObDetail::where('tc_rooting_bottle_id',$bottleId)->get()->toArray());
        $minBottleOb = $dt->sum('bottle_oxidate') + $dt->sum('bottle_contam') + $dt->sum('bottle_other');
        // transfer back
        $minBottleTransfer = 0;
        $q = TcRootingTransferBottle::where('tc_rooting_bottle_id',$bottleId)->get();
        foreach ($q as $key => $value) {
            $minBottleTransfer += ($value['bottle_rooting']-$value['bottle_left']); 
        }
        
        $return = $minBottleOb + $minBottleTransfer;  
        return $return;
    }

}
