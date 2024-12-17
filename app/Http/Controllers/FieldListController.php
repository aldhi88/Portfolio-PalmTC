<?php

namespace App\Http\Controllers;

use App\Models\TcField;
use App\Models\TcFieldTree;
use App\Models\TcInit;
use App\Models\TcSample;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FieldListController extends Controller
{
    public function index()
    {
        $data['title'] = "Field Per Sample";
        $data['desc'] = "Display all available data";
        return view('modules.field_list.index',compact('data'));
    }

    public function dt()
    {
        $data = TcInit::select([
                'tc_inits.id',
                'tc_inits.tc_sample_id',
            ])
            ->whereHas('tc_fields')
            ->with([
                'tc_samples:id,sample_number,program',
                'tc_fields:id,tc_init_id',
                'tc_fields.tc_field_trees' => function($q){
                    $q->select('id','tc_field_id');
                },
            ])
            ->withCount([
                'tc_field_trees as total_data' => function($q){
                    $q->where('tc_field_trees.status','!=',0);
                }
            ])
            ->withCount([
                'tc_fields as total_date' => function($q){
                    $q->where('tc_fields.status',1);
                }
            ])
            ->withCount([
                'tc_field_trees as total_active' => function($q){
                    $q->where('tc_field_trees.status',1);
                }
            ])
            ->withCount([
                'tc_field_ob_details as total_death' => function($q){
                    $q->where('is_death',1)
                        ->whereHas('tc_field_trees', function($q2){
                            $q2->whereHas('tc_fields',function($q3){
                                $q3->where('status',1);
                            });
                        });
                }
            ])
        ;
        
        return DataTables::of($data)
            ->addColumn('sample_number_format',function($data){
                $el = '<p class="mb-0"><strong>'.$data->tc_samples->sample_number_display.'</strong></p>';
                $el .= '
                    <p class="mb-0">
                        <a class="text-primary" href="'.route('field-lists.show',$data->id).'">Detail</a>
                ';
                $el .= '</p>';
                return $el;
            })
            ->rawColumns(['sample_number_format'])
            ->smart(false)->toJson();
    }

    public function show($id)
    {
        $data['title'] = "Field List Data";
        $data['desc'] = "Display all fieldsery bottle list";
        $data['initId'] = $id;
        $data['sampleNumber'] = TcSample::select('id','sample_number')
            ->whereHas('tc_inits', function(Builder $q) use($id){
                $q->where('id',$id);
            })
            ->first()->getAttribute('sample_number_display');
        return view('modules.field_list.show',compact('data'));
    }

    public function dtShow(Request $request)
    {
        $data = TcField::select([
                'tc_fields.*',
                DB::raw('convert(varchar,tree_date, 103) as tree_date_format')
            ])
            ->where('tc_init_id',$request->initId)
            ->with([
                'tc_inits',
                'tc_inits.tc_samples',
                'tc_workers',
            ])
            ->withCount(['tc_field_trees as total_data'])
            ->withCount(['tc_field_trees as total_active' => function($q){
                $q->where('status',1);
            }])
            ->withCount([
                'tc_field_ob_details as total_death' => function($q){
                    $q->where('is_death',1);
                }
            ])
        ;
        if($request->filter == 1 || !isset($request->filter)){
            $data->where('status','!=',0);
        }

        return DataTables::of($data)
            ->filterColumn('tree_date_format', function($query, $keyword) {
                $sql = 'convert(varchar,tree_date, 103) like ?';
                $query->whereRaw($sql, ["{$keyword}"]);
            })
            ->addColumn('tree_date_action',function($data){
                $el = '<p class="mb-0"><strong>'.$data->tree_date_format.'</strong></p>';
                $el .= '
                    <p class="mb-0">
                        <a class="text-primary detail" data-date="'.$data->tree_date_format.'" data-id="'.$data->id.'" href="#'.$data->id.'">Detail</a>
                ';
                $el .= '</p>';
                return $el;
            })
            ->rawColumns(['tree_date_action'])
            ->smart(false)->toJson();
    }

    public function dtShow2(Request $request)
    {
        $fieldId = $request->filter;
        $data = TcFieldTree::select([
                'tc_field_trees.*',
                DB::raw('convert(varchar,tc_fields.tree_date, 103) as tree_date_format'),
            ])
            ->where('tc_field_id',$fieldId)
            ->where('tc_field_trees.status','<',2)
            ->leftJoin('tc_fields','tc_fields.id','=','tc_field_trees.tc_field_id');
        return DataTables::of($data)
            ->addColumn('status_format',function($data){
                if($data->status==1){
                    $badge = "on text-primary";
                }else{
                    $badge = "off text-secondary";
                }
                return '<i data-id="'.$data->id.'" data-status="'.$data->status.'" class="switch fas fa-toggle-'.$badge.'"></i>';
            })
            ->addColumn('skor_akar',function($data){
                $el = '<input type="number" class="w-100 skor-akar text-center" value="'.$data->skor_akar.'" name="skor_akar_'.$data->id.'" data-id="'.$data->id.'">';
                return $el;
            })
            ->rawColumns(['status_format','skor_akar'])
            ->smart(false)->toJson();
    }

    public function changeStatus(Request $request)
    {
        TcFieldTree::where("id",$request->id)->update(["status" => $request->status]);
        return response()->json([
            'status' => 'success',
            'data' => [
                'status' => $request->status,
                'id' => $request->id,
            ],
        ]);
    }

    public function changeSkorAkar(Request $request)
    {
        TcFieldTree::where('id',$request->id)->update(['skor_akar' => $request->skor_akar]);
    }
}
