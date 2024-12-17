<?php

namespace App\Http\Controllers;

use App\Exports\SamplesExport;
use App\Http\Requests\SampleCreate;
use App\Http\Requests\SampleEdit;
use App\Models\MasterTreefile;
use App\Models\TcSample;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF;
use Illuminate\Support\Str;

class SampleController extends Controller
{

    public function exportPrint(Request $request){
        $from = $request->from.'-01-01 00:00:00';
        $to = $request->to.'-12-31 23:59:59';
        $data['samples'] = TcSample::select(
            DB::raw('
                *,
                YEAR(created_at) as year,
                MONTH(created_at) as month
            '))
            ->whereBetween('created_at',[$from,$to])
            ->orderBy('created_at','asc')
            ->get();
        $data['title'] = "Export to PDF";
        $data['desc'] = "Display layout before convert to PDF file.";
        return view('modules.sample.print.download_print', compact('data'));
    }
    public function exportExcel(Request $request){
        $dateTime = Carbon::now()->getPreciseTimestamp(3);
        $fileName = now()->format('Y_m_d_').substr(md5($dateTime),0,8).".xlsx";
        Storage::makeDirectory("public/excel_temp");
        (new SamplesExport($request->all()))->store($fileName, 'dir_excel_temp');
        return response()->json([
            'data' => [
                'link' => asset("storage/excel_temp"),
                'filename' => $fileName,
            ],
        ]);
    }
    public function exportPDF(Request $request){
        $from = $request->from.'-01-01 00:00:00';
        $to = $request->to.'-12-31 23:59:59';
        $data['samples'] = TcSample::select(
            DB::raw('
                *,
                YEAR(created_at) as year,
                MONTH(created_at) as month
            '))
            ->whereBetween('created_at',[$from,$to])
            ->orderBy('created_at','asc')
            ->get();
        $data['title'] = "Export to PDF";
        $data['desc'] = "Display layout before convert to PDF file.";
        $pdf = PDF::loadView('modules.sample.print.download_pdf', $data)->setPaper('a4', 'landscape');

        $dateTime = Carbon::now()->getPreciseTimestamp(3);
        $fileName = now()->format('Y_m_d_').substr(md5($dateTime),0,8).".pdf";
        // return $pdf->download($fileName);
        Storage::makeDirectory("public/pdf_temp");
        $path = public_path("storage/pdf_temp/");
        $pdf->save($path . $fileName);
        return response()->json([
            'data' => [
                'link' => asset('storage/pdf_temp'),
                'filename' => $fileName,
            ],
        ]);
    }

    public function index()
    {
        $data['title'] = "Sample Data";
        $data['desc'] = "Display all available Sample data";

        $dtSamples = TcSample::select(
                "created_at",
                DB::raw('YEAR(created_at) year')
            )
            ->orderBy("created_at","ASC")
            ->get();
        $data["years"] = [];
        foreach ($dtSamples as $key => $value) {
            if(!in_array($value->year,$data["years"])){
                array_push($data["years"],$value->year);
            }
        }
        return view('modules.sample.index', compact('data'));
    }

    public function getTreefile(){
        $TcSample = new TcSample();
        return view('modules.sample.treefile');
    }
    public function getSample(){
        return view('modules.sample.sample');
    }

    public function getData($id){
        $TcSample = new TcSample();
        $data = $TcSample->selByCol('id', $id)->toArray();
        // error #001
        return response()->json([
            'data' => [
                'data' => $data,
            ],
        ]);
    }
    public function dtTreefile(){
        $data = MasterTreefile::whereNotNull('noseleksi');
        return DataTables::eloquent($data)
            ->addColumn('status',function($data){
                $cek = TcSample::where('master_treefile_id',$data->id)->get()->count();
                if($cek==0){
                    $return = '
                        <label class="badge badge-light-primary d-block w-100 my-0 rounded-0">New</label>
                    ';
                }else{
                    $return = '
                        <label class="badge badge-light-danger d-block w-100 my-0 rounded-0">Resample</label>
                    ';
                }
                return $return;
            })
            ->addColumn('noseleksi_link', function($data){
                $cek = TcSample::where('master_treefile_id',$data->id)->get()->count();
                $resample=0;
                if($cek!=0){
                    $resample = 1;
                }
                return '<a resampel="'.$resample.'" noseleksi="'.$data->noseleksi.'" id="'.$data->id.'" href="#" class="text-primary">'.$data->noseleksi.'</a>';
            })
            ->rawColumns(['noseleksi_link','status'])
            ->toJson();
    }
    public function dtSample(){
        $TcSample = new TcSample();
        $data = $TcSample->dtSample();
        return DataTables::eloquent($data)
                ->addColumn('sample_number_link', function($data){
                    return '<a id="'.$data->id.'" display="'.$data->sample_number_display.'" id-treefile="'.$data->master_treefile_id.'" noseleksi="'.$data->master_treefile->noseleksi.'" href="#" class="text-primary">'.$data->sample_number_display.'</a>';
                })
                ->rawColumns(['sample_number_link', 'resample_display'])
                ->toJson();
    }
    public function dt(){
        $data = TcSample::select(
            DB::raw('
                tc_samples.*,
                YEAR(created_at) as year,
                MONTH(created_at) as month
            '))
            ->with("master_treefile")
            ;
        return DataTables::of($data)
            ->addColumn('custom_name', function($data){
                $editLink = route('samples.edit', $data->id);
                $el = '<strong class="mt-0 font-size-14">'.$data->sample_number_display.'</strong>';
                $el .= "
                    <p class='mb-0 font-size-14'>
                        <a class='text-primary' data-id='".$data->id."' href='".$editLink."'>Modify</a>
                ";
                $cekAdaResample = TcSample::where('resample',$data->id)->get()->count();
                if($cekAdaResample==0){
                    $el .= "
                            <a class='text-danger' data-id='".$data->id."' href='#' data-toggle='modal' data-target='#deleteModal'>Delete</a>
                        ";
                }
                $el .= '</p>';
                return $el;
            })
            ->rawColumns(['custom_name', 'resample_display'])
            ->toJson();
    }

    public function getSampleNumb(){
        $TcSample = new TcSample();
        $data['sample_number'] = $TcSample->newSampleNumb();
        $data['display_number'] = $TcSample->displayNumb();
        return response()->json([
            'data' => [
                'data' => $data,
            ],
        ]);
    }

    public function create(){
        $data['title'] = "Create New Sample";
        $data['desc'] = "Add new Sample form";

        $TcSample = new TcSample();
        $data['sample_number'] = $TcSample->newSampleNumb();
        $data['display_number'] = $TcSample->displayNumb();

        return view('modules.sample.create', compact('data'));
    }
    public function store(SampleCreate $request)
    {
        $TcSample = new TcSample();
        $data = $request->except('_token', 'no_seleksi','baris');
        $cekResample = TcSample::where('master_treefile_id',$request->master_treefile_id)
            ->whereNull('resample')
            ->get();
        if($cekResample->count()!=0){
            $data['resample'] = $cekResample->first()->id;
        }
        $data['program'] = Str::upper($data['program']);
        TcSample::create($data);
        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, new data has been added.',
            ],
        ]);
    }

    public function edit($id)
    {
        $data['title'] = "Edit Sample Data";
        $data['desc'] = "Change Sample data with correct data";

        $TcSample = new TcSample();
        $data['data_edit'] = $TcSample->selByCol('id', $id)->first();
        $data['data_edit']['created_at_edit'] = Carbon::parse($data['data_edit']->created_at)->format('Y-m-d');
        return view('modules.sample.edit', compact('data'));
    }


    public function update(SampleEdit $request, $id)
    {
        $data = $request->except('_token', '_method', 'id', 'no_seleksi');
        $data['program'] = Str::upper($data['program']);
        $TcSample = new TcSample();
        $TcSample->upData($id, $data);
        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, data updated successfully.',
            ],
        ]);
    }

    public function destroy($id)
    {
        $TcSample = new TcSample();
        $TcSample->delData($id);
        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => 'success',
                'icon' => 'check',
                'el' => 'alert-area',
                'msg' => 'Success, data deleted successfully.',
            ],
        ]);
    }
}
