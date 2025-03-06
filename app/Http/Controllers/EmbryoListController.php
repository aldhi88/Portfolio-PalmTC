<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmbryoBottleSubtractionCreate;
use App\Models\TcEmbryoBottle;
use App\Models\TcEmbryoBottleSubtraction;
use App\Models\TcEmbryoList;
use App\Models\TcEmbryoOb;
use App\Models\TcEmbryoTransfer;
use App\Models\TcInit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class EmbryoListController extends Controller
{
    public function index()
    {
        $data['title'] = "Embryogenesis Per Sample";
        $data['desc'] = "Display all embryogenesis";
        return view('modules.embryo_list.index',compact('data'));
    }
    public function dt()
    {
        $data = TcInit::select(['tc_inits.id','tc_inits.tc_sample_id'])
            ->whereHas('tc_embryo_bottles')
            ->with([
                'tc_samples:id,sample_number,program',
            ])
            ->withCount([
                'tc_embryo_bottles as first_total' => function($q){
                    $q->select(DB::raw('SUM(number_of_bottle) as first_total'))->where('status','!=',0);
                }
            ])
        ;
        return DataTables::of($data)
            ->addColumn('sample_number_format',function($data){
                $el = '<p class="mb-0"><strong>'.$data->tc_samples->sample_number_display.'</strong></p>';
                $el .= '
                    <p class="mb-0">
                        <a class="text-primary" href="'.route('embryo-lists.show',$data->id).'">Detail</a>
                ';
                $el .= '</p>';
                return $el;
            })
            ->addColumn('total_bottle_active',function($data){
                if (is_null($data->first_total)) {
                    return 0;
                }
                $q = TcEmbryoBottle::select('id')
                    ->where('tc_init_id',$data->id)
                    ->where('status','!=',0)
                    ->get();
                $usedBottle = 0;
                foreach ($q as $key => $value) {
                    $usedBottle += TcEmbryoBottle::usedBottle($value->id);
                }
                return $data->first_total - $usedBottle;
            })
            ->rawColumns(['sample_number_format'])
            ->smart(false)
            ->toJson();
    }

    public function show($id)
    {
        $data['title'] = "Embryogenesis List Data";
        $data['desc'] = "Display all embryogenesis bottle list";
        $data['initId'] = $id;
        $q = TcInit::where('id',$id)->first();
        $data['sampleNumber'] = $q->tc_samples->sample_number_display;
        return view('modules.embryo_list.show',compact('data'));
    }
    public function dtShow(Request $request)
    {
        $data = TcEmbryoBottle::select([
                'tc_embryo_bottles.*',
                DB::raw('convert(varchar,bottle_date, 103) as bottle_date_format')
            ])
            ->where('tc_init_id',$request->initId)
            ->with([
                'tc_inits',
                'tc_inits.tc_samples',
                'tc_workers',
            ])
        ;

        if($request->filter == 1 || !isset($request->filter)){
            $data->where('status',1);
        }

        return DataTables::of($data)
            ->filterColumn('bottle_date_format', function($query, $keyword) {
                $sql = 'convert(varchar,bottle_date, 103) like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('last_total',function($data){
                return $data->number_of_bottle - TcEmbryoBottle::usedBottle($data->id);
            })
            ->rawColumns(['date_work_format'])
            ->smart(false)
            ->toJson();
    }
    public function dtShow2(Request $request)
    {
        $qCode = 'DATE_FORMAT(tc_embryo_bottles.bottle_date, "%d/%m/%Y")';
        if(config('database.default') == 'sqlsrv'){
            $qCode = 'convert(varchar,tc_embryo_bottles.bottle_date, 103)';
        }
        $data = TcEmbryoList::select([
                'tc_embryo_lists.*',
                DB::raw($qCode.' as bottle_date_format')
            ])
            ->leftJoin('tc_embryo_bottles','tc_embryo_bottles.id','=','tc_embryo_lists.tc_embryo_bottle_id')
            ->with([
                'tc_inits.tc_samples',
                'tc_embryo_bottles',
                'tc_embryo_bottles.tc_workers',
                'tc_workers:id,code',
            ])
            ->where('tc_embryo_lists.tc_init_id',$request->initId)
            ->whereHas('tc_embryo_bottles',function(Builder $q){
                $q->where('status','!=',0);
            })
        ;
        return DataTables::of($data)
            ->filterColumn('bottle_date_format', function($query, $keyword) use($qCode) {
                $sql = $qCode.'  like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('obs_date',function($data){
                $return = $data->tc_embryo_ob_id;
                if(!is_null($data->tc_embryo_ob_id)){
                    $return = TcEmbryoOb::where('id',$data->tc_embryo_ob_id)
                        ->first()
                        ->getAttribute('work_date');
                    $return = Carbon::parse($return)->format('d/m/Y');
                }
                return $return;
            })
            ->addColumn('transfer_date',function($data){
                $return = $data->tc_embryo_transfer_id;
                if(!is_null($data->tc_embryo_transfer_id)){
                    $return = TcEmbryoTransfer::where('id',$data->tc_embryo_transfer_id)
                        ->first()
                        ->getAttribute('transfer_date');
                    $return = Carbon::parse($return)->format('d/m/Y');
                }
                return $return;
            })
            ->smart(false)
            ->rawColumns([])
            ->toJson();
    }
    public function showSubtraction(Request $request)
    {
        $data['subtractions'] = TcEmbryoBottleSubtraction::where('tc_embryo_bottle_id',$request->id)
            ->get();
        return view('modules.embryo_list.view_subtraction',compact('data'));
    }
    public function storeSubtraction(EmbryoBottleSubtractionCreate $request)
    {
        $data = $request->except('_token');
        TcEmbryoBottleSubtraction::create($data);
        TcEmbryoBottle::where('id',$request->tc_embryo_bottle_id)
            ->decrement('number_of_bottle',$request->bottle_count);
        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, total bottle has been changed.',
            ],
        ]);
    }
    public function destroySubtraction(Request $request)
    {
        $q = TcEmbryoBottleSubtraction::where('id',$request->id)
            ->first();
        $bottleCount = $q->bottle_count;
        $bottleId = $q->tc_embryo_bottle_id;
        TcEmbryoBottleSubtraction::where('id',$request->id)
            ->forceDelete();
        TcEmbryoBottle::where('id',$bottleId)
            ->increment('number_of_bottle',$bottleCount);

        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area-delSubtraction',
                'msg' => 'Success, data has been delete.',
                'id' => $bottleId
            ],
        ]);
    }
}
