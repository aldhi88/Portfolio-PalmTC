@extends('layouts.master')
@section('css')
@include('modules.field_ob.include.show_css')
@endsection

@section('js')
@include('modules.field_ob.include.show_js')
@endsection

@section('content')
<span id="alert-area"></span>
<div class="row">
    <div class="col">
        

        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <h5><i class="feather icon-file-text"></i> All Data Per Date</h5>
                        <a href="{{ route('field-obs.index') }}" class="btn btn-warning btn-sm has-ripple"><i class="fas fa-backward mr-2"></i>Back</a>
                    </div>
                    <div class="col text-right">
                        <h5>Sample:</h5>
                        <span class="badge-light text-dark font-weight-bold px-2 py-1 border">
                            <i class="fas fa-eye-dropper mr-2"></i>{{ $data['sampleNumber'] }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                    <thead>
                        <tr>
                            <th rowspan="2">Nursery Date</th>
                            <th rowspan="2">Prog</th>
                            <th rowspan="2">Sample</th>
                            <th rowspan="2">Worker</th>
                            <th rowspan="2">Obs<br>Count</th>
                            <th rowspan="2">First<br>Total</th>
                            <th colspan="{{ count($data['death'])+1 }}" class="text-center bg-danger text-white">Death</th>
                            <th rowspan="2">Normal</th>
                            <th rowspan="2">Abnormal</th>
                            <th rowspan="2">Panen</th>
                        </tr>
                        <tr>
                            @foreach ($data['death'] as $item)
                                <th class="bg-warning">{{ $item['code'] }}</th>
                            @endforeach
                            <th class="bg-danger text-white">Total</th>
                        </tr>
                    </thead>
                    <thead id="header-filter" class="bg-white">
                        <tr>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white" disable="true"></th>
                            @foreach ($data['death'] as $item)
                            <th class="bg-white" disable="true"></th>
                            @endforeach
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white" disable="true"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            
        </div>

    </div>
</div>


<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <h5><i class="feather icon-file-text"></i> Observation List</h5>
                    </div>
                    <div class="col text-right">
                        <h5><i class="feather icon-calendar"></i> Nursery Date : <span id="select-data">-</span></h5>
                        <input type="hidden" name="tab2Filter" value="0">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <span id="alert-area2"></span>
                <table id="myTable2" class="table table-striped table-bordered nowrap table-xs w-100">
                    <thead>
                        <tr>
                            <th rowspan="2">Nursery Date</th>
                            <th rowspan="2">Obs Date</th>
                            <th colspan="{{ count($data['death'])+1 }}" class="text-center bg-danger text-white">Death</th>
                            <th rowspan="2">Normal</th>
                            <th rowspan="2">Abnormal</th>
                            <th rowspan="2">Panen</th>
                        </tr>
                        <tr>
                            @foreach ($data['death'] as $item)
                                <th class="bg-warning">{{ $item['code'] }}</th>
                            @endforeach
                            <th class="bg-danger text-white">Total</th>
                        </tr>
                    </thead>
                    <thead id="header-filter2" class="bg-white">
                        <tr>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white"></th>
                            @foreach ($data['death'] as $item)
                            <th class="bg-white" disable="true"></th>
                            @endforeach
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white" disable="true"></th>
                            <th class="bg-white" disable="true"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            
        </div>

    </div>
</div>

@include('modules.field_ob.include.modal')
@endsection




