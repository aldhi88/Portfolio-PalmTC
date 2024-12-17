@extends('layouts.master')
@section('css')
@include('modules.rooting_list.include.index_css')
@endsection

@section('js')
@include('modules.rooting_list.include.index_js')
@endsection

@section('content')
<span id="alert-area"></span>

<div class="row">
    <div class="col">

        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="col"><h5><i class="feather icon-file-text"></i> All Data Bottle</h5></div>
                </div>
            </div>
            <div class="card-body">
                <table id="myTable" class="table table-striped table-bordered nowrap table-xs w-100">
                    <thead>
                        <tr>
                            <th>Sample</th>
                            <th>Program</th>
                            <th>{{ $data['column1'] }}</th>
                            <th>Plantlet</th>
                            <th>{{ $data['column2'] }}</th>
                            <th>Total Bottle</th>
                            <th>Total Plantlet</th>
                        </tr>
                    </thead>
                    <thead id="header-filter" class="bg-white">
                        <tr>
                            <th class="bg-white"></th>
                            <th class="bg-white"></th>
                            <th class="bg-white" disable="true"></th>
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
@endsection




