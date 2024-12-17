<?php

namespace App\Http\Controllers;

use App\Models\TcBottleInit;
use App\Models\TcBottleInitDetail;
use App\Models\TcInit;
use App\Models\TcRootingBottle;
use App\Models\TcRootingOb;
use App\Models\TcRootingTransaction;
use App\Models\TcRootingTransfer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RootingListController extends Controller
{
    public function index()
    {
        $data['title'] = "Rooting Per Sample";
        $data['desc'] = "Display all available data";
        $data['column1'] = TcBottleInit::where('keyword','rooting_column1')->first()->getAttribute('column_name');
        $data['column2'] = TcBottleInit::where('keyword','rooting_column2')->first()->getAttribute('column_name');
        return view('modules.rooting_list.index',compact('data'));
    }
    public function dt()
    {
        $q = TcBottleInitDetail::select('tc_bottle_id')
            ->whereHas('tc_bottle_inits',function(Builder $q){
                $q->where('keyword','rooting_column1');
            })->get()->toArray();
        $aryBottleCol1 = array_column($q, 'tc_bottle_id');
        $q = TcBottleInitDetail::select('tc_bottle_id')
            ->whereHas('tc_bottle_inits',function(Builder $q){
                $q->where('keyword','rooting_column2');
            })->get()->toArray();
        $aryBottleCol2 = array_column($q, 'tc_bottle_id');
        $data = TcInit::select(['tc_inits.*'])
            ->whereHas('tc_rooting_bottles')
            ->with([
                'tc_samples',
            ])
            ->withCount([
                'tc_rooting_bottles as first_total' => function($q){
                    $q->select(DB::raw('SUM(bottle_count)'));
                }
            ])
            ->withCount([
                'tc_rooting_bottles as first_total_leaf' => function($q){
                    $q->select(DB::raw('SUM(leaf_count)'));
                }
            ])
            ->withCount([
                'tc_rooting_bottles as first_total_column1' => function($q) use($aryBottleCol1){
                    $q->select(DB::raw('SUM(bottle_count)'))->whereIn('tc_bottle_id',$aryBottleCol1)
                        ->where('status','!=',0)->where('type',1);
                }
            ])
            ->withCount([
                'tc_rooting_bottles as first_total_explant' => function($q) use($aryBottleCol1){
                    $q->select(DB::raw('SUM(leaf_count)'))->whereIn('tc_bottle_id',$aryBottleCol1)
                        ->where('status','!=',0)->where('type',1);
                }
            ])
            ->withCount([
                'tc_rooting_bottles as first_total_column2' => function($q) use($aryBottleCol2){
                    $q->select(DB::raw('SUM(bottle_count)'))->whereIn('tc_bottle_id',$aryBottleCol2)
                        ->where('status','!=',0);
                }
            ])
        ;
        return DataTables::of($data)
            ->addColumn('sample_number_format',function($data){
                $el = '<p class="mb-0"><strong>'.$data->tc_samples->sample_number_display.'</strong></p>';
                $el .= '
                    <p class="mb-0">
                        <a class="text-primary" href="'.route('rooting-lists.show',$data->id).'">Detail</a>
                ';
                $el .= '</p>';
                return $el;
            })
            ->addColumn('column1',function($data){
                $q = TcBottleInitDetail::select('tc_bottle_id')
                    ->whereHas('tc_bottle_inits',function(Builder $q){
                        $q->where('keyword','rooting_column1');
                    })->get()->toArray();
                $aryBottleId = array_column($q, 'tc_bottle_id');
                $q = TcRootingBottle::select('id')->where('tc_init_id',$data->id)
                    ->whereIn('tc_bottle_id',$aryBottleId)->get()
                    ->where('status','!=',0)->where('type',1)
                ;
                $usedBottle = 0;
                foreach ($q as $key => $value) {
                    $usedBottle += TcRootingBottle::usedBottle($value->id);
                }
                return $data->first_total_column1 - $usedBottle;
            })
            ->addColumn('explant1',function($data){
                $q = TcBottleInitDetail::select('tc_bottle_id')
                    ->whereHas('tc_bottle_inits',function(Builder $q){
                        $q->where('keyword','rooting_column1');
                    })->get()->toArray();
                $aryBottleId = array_column($q, 'tc_bottle_id');
                $q = TcRootingBottle::select('id')->where('tc_init_id',$data->id)
                    ->whereIn('tc_bottle_id',$aryBottleId)->get()
                    ->where('status','!=',0)->where('type',1);
                $usedBottle = 0;
                foreach ($q as $key => $value) {
                    $usedBottle += TcRootingBottle::usedBottleLeaf($value->id);
                }
                return $data->first_total_explant - $usedBottle;
            })
            ->addColumn('column2',function($data){
                $q = TcBottleInitDetail::select('tc_bottle_id')
                    ->whereHas('tc_bottle_inits',function(Builder $q){
                        $q->where('keyword','rooting_column2');
                    })->get()->toArray();
                $aryBottleId = array_column($q, 'tc_bottle_id');

                $q = TcRootingBottle::select('id')->where('tc_init_id',$data->id)
                    ->whereIn('tc_bottle_id',$aryBottleId)->get()
                    ->where('status','!=',0)->where('type',2);
                $usedBottle = 0;
                foreach ($q as $key => $value) {
                    $usedBottle += TcRootingBottle::usedBottle($value->id);
                }
                return $data->first_total_column2 - $usedBottle;
            })
            ->addColumn('total_bottle_active',function($data){
                $q = TcRootingBottle::select('id')->where('tc_init_id',$data->id)->get();
                $usedBottle = 0;
                foreach ($q as $key => $value) {
                    $usedBottle += TcRootingBottle::usedBottle($value->id);
                }
                return $data->first_total - $usedBottle;
            })
            ->addColumn('total_leaf_active',function($data){
                $q = TcRootingBottle::select('id')->where('tc_init_id',$data->id)->get();
                $usedBottle = 0;
                foreach ($q as $key => $value) {
                    $usedBottle += TcRootingBottle::usedBottleLeaf($value->id);
                }
                return $data->first_total_leaf - $usedBottle;
            })
            ->rawColumns(['sample_number_format'])
            ->smart(false)
            ->toJson();
    }

    public function show($id)
    {
        $data['title'] = "Rooting List Data";
        $data['desc'] = "Display all rooting bottle list";
        $data['initId'] = $id;
        $q = TcInit::where('id',$id)->first();
        $data['sampleNumber'] = $q->tc_samples->sample_number_display;
        $data['column1'] = TcBottleInit::where('keyword','rooting_column1')->first()->getAttribute('column_name');
        $data['column2'] = TcBottleInit::where('keyword','rooting_column2')->first()->getAttribute('column_name');
        return view('modules.rooting_list.show',compact('data'));
    }
    public function dtShow(Request $request)
    {
        $qCode = 'DATE_FORMAT(bottle_date, "%d/%m/%Y")';
        if(config('database.default') == 'sqlsrv'){
            $qCode = 'convert(varchar,bottle_date, 103)';
        }
        $list = ['rooting_column1','rooting_column2'];
        $q = TcBottleInit::whereIn('keyword',$list)->get();
        foreach ($q as $key => $value) {
            foreach ($value->tc_bottle_init_details as $key2 => $value2) {
                $bottleList[] = $value2->tc_bottle_id;
            }
        }
        $data = TcRootingBottle::select([
                'tc_rooting_bottles.*',
                DB::raw($qCode.' as bottle_date_format')
            ])
            ->whereIn('tc_bottle_id',$bottleList)
            ->where('tc_init_id',$request->initId)
            ->with([
                'tc_inits',
                'tc_inits.tc_samples',
                'tc_workers',
                'tc_bottles'
            ])
        ;
        if($request->filter == 1 || !isset($request->filter)){
            $data = TcRootingBottle::select([
                    'tc_rooting_bottles.*',
                    DB::raw($qCode.' as bottle_date_format')
                ])
                ->whereIn('tc_bottle_id',$bottleList)
                ->where('tc_init_id',$request->initId)
                ->where('status',1)
                ->with([
                    'tc_inits',
                    'tc_inits.tc_samples',
                    'tc_workers',
                    'tc_bottles'
                ])
            ;
        }
        // dd($data->get()->toArray());
        return DataTables::of($data)
            ->filterColumn('bottle_date_format', function($query, $keyword) use($qCode) {
                $sql = $qCode.'  like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('last_total',function($data){
                return $data->bottle_count - TcRootingBottle::usedBottle($data->id);
            })
            ->addColumn('last_total_leaf',function($data){
                return $data->leaf_count - TcRootingBottle::usedBottleLeaf($data->id);
            })
            ->addColumn('column1',function($data){
                $q = TcBottleInit::where('keyword','rooting_column1')->with('tc_bottle_init_details')->get();
                $dataBottle = $q[0]->tc_bottle_init_details;
                $total = 0;
                foreach ($dataBottle as $key => $value) {
                    $bottleId = $value->tc_bottle_id;
                    if($bottleId == $data->tc_bottle_id){
                        $total = $total + $data->bottle_count;
                    }
                }
                return $total;
            })
            ->addColumn('explant1',function($data){
                $q = TcBottleInit::where('keyword','rooting_column1')->with('tc_bottle_init_details')->get();
                $dataBottle = $q[0]->tc_bottle_init_details;
                $total = 0;
                foreach ($dataBottle as $key => $value) {
                    $bottleId = $value->tc_bottle_id;
                    if($bottleId == $data->tc_bottle_id){
                        $total = $total + $data->leaf_count;
                    }
                }
                return $total;
            })
            ->addColumn('column2',function($data){
                $q = TcBottleInit::where('keyword','rooting_column2')->with('tc_bottle_init_details')->get();
                $dataBottle = $q[0]->tc_bottle_init_details;
                $total = 0;
                foreach ($dataBottle as $key => $value) {
                    $bottleId = $value->tc_bottle_id;
                    if($bottleId == $data->tc_bottle_id){
                        $total = $total + $data->bottle_count;
                    }
                }
                return $total;
            })
            ->rawColumns(['date_work_format'])
            ->smart(false)
            ->toJson();
    }
    public function dtShow2(Request $request)
    {
        $qCode = 'DATE_FORMAT(tc_rooting_bottles.bottle_date, "%d/%m/%Y")';
        if(config('database.default') == 'sqlsrv'){
            $qCode = 'convert(varchar,tc_rooting_bottles.bottle_date, 103)';
        }
        $data = TcRootingTransaction::select([
                'tc_rooting_transactions.*',
                DB::raw($qCode.' as bottle_date_format')
            ])
            ->leftJoin('tc_rooting_bottles','tc_rooting_bottles.id','=','tc_rooting_transactions.tc_rooting_bottle_id')
            ->with([
                'tc_inits.tc_samples',
                'tc_rooting_bottles',
                'tc_rooting_bottles.tc_workers',
                'tc_workers:id,code',
            ])
            ->whereHas('tc_rooting_bottles',function(Builder $q){
                $q->where('status','!=',0);
            })
        ;
        return DataTables::of($data)
            ->filterColumn('bottle_date_format', function($query, $keyword) use($qCode) {
                $sql = $qCode.'  like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('obs_date',function($data){
                $return = $data->tc_rooting_ob_id;
                if(!is_null($return)){
                    $return = TcRootingOb::where('id',$data->tc_rooting_ob_id)->first()->getAttribute('ob_date');
                    $return = Carbon::parse($return)->format('d/m/Y');
                }
                return $return;
            })
            ->addColumn('transfer_date',function($data){
                $return = $data->tc_rooting_transfer_id;
                if(!is_null($data->tc_rooting_transfer_id)){
                    $return = TcRootingTransfer::where('id',$data->tc_rooting_transfer_id)->first()->getAttribute('transfer_date');
                    $return = Carbon::parse($return)->format('d/m/Y');
                }
                return $return;
            })
            ->smart(false)
            ->rawColumns([])
            ->toJson();
    }
}
