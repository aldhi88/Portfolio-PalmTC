@extends('layouts.master')

@section('content')
<div class="card">

<div class="card-header">
    <h5>Master Data</h5>
    <div class="card-header-right">
        <div class="btn-group card-option">
            <button type="button" class="btn dropdown-toggle btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="feather icon-more-horizontal"></i>
            </button>
            <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                <li class="dropdown-item minimize-card"><a href="#!"><span><i class="feather icon-minus"></i> collapse</span><span style="display:none"><i class="feather icon-plus"></i> expand</span></a></li>
            </ul>
        </div>
    </div>
</div>


<div class="card-body">
    <div class="row">
        <div class="col-sm-3">
            <a href="#">
                <div class="card bg-c-blue text-white widget-visitor-card">
                    <div class="card-body text-center p-0 pt-2">
                        <h5 class="text-white">Worker</h5>
                        <i class="feather icon-users"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-3">
            <a href="#">
                <div class="card bg-c-blue text-white widget-visitor-card">
                    <div class="card-body text-center p-0 pt-2">
                        <h5 class="text-white">Bottle</h5>
                        <i class="fas fa-flask"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-3">
            <a href="#">
                <div class="card bg-c-blue text-white widget-visitor-card">
                    <div class="card-body text-center p-0 pt-2">
                        <h5 class="text-white">Agar Rose</h5>
                        <i class="fas fa-flask"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-3">
            <a href="#">
                <div class="card bg-c-blue text-white widget-visitor-card">
                    <div class="card-body text-center p-0 pt-2">
                        <h5 class="text-white">Medium</h5>
                        <i class="fas fa-flask"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-3">
            <a href="#">
                <div class="card bg-c-blue text-white widget-visitor-card">
                    <div class="card-body text-center p-0 pt-2">
                        <h5 class="text-white">Medium Stock</h5>
                        <i class="fas fa-flask"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-3">
            <a href="#">
                <div class="card bg-c-blue text-white widget-visitor-card">
                    <div class="card-body text-center p-0 pt-2">
                        <h5 class="text-white">Stock Validation</h5>
                        <i class="fas fa-flask"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-3">
            <a href="#">
                <div class="card bg-c-blue text-white widget-visitor-card">
                    <div class="card-body text-center p-0 pt-2">
                        <h5 class="text-white">Bottle Columns</h5>
                        <i class="fas fa-th-list"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-3">
            <a href="#">
                <div class="card bg-c-blue text-white widget-visitor-card">
                    <div class="card-body text-center p-0 pt-2">
                        <h5 class="text-white">Laminar</h5>
                        <i class="fas fa-weight"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-3">
            <a href="#">
                <div class="card bg-c-blue text-white widget-visitor-card">
                    <div class="card-body text-center p-0 pt-2">
                        <h5 class="text-white">Plantation</h5>
                        <i class="feather icon-map"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-3">
            <a href="#">
                <div class="card bg-c-blue text-white widget-visitor-card">
                    <div class="card-body text-center p-0 pt-2">
                        <h5 class="text-white">Room</h5>
                        <i class="feather icon-map-pin"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-3">
            <a href="#">
                <div class="card bg-c-blue text-white widget-visitor-card">
                    <div class="card-body text-center p-0 pt-2">
                        <h5 class="text-white">Contamination</h5>
                        <i class="fas fa-bug"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-3">
            <a href="#">
                <div class="card bg-c-blue text-white widget-visitor-card">
                    <div class="card-body text-center p-0 pt-2">
                        <h5 class="text-white">Death</h5>
                        <i class="fas fa-biohazard"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-3">
            <a href="#">
                <div class="card bg-c-blue text-white widget-visitor-card">
                    <div class="card-body text-center p-0 pt-2">
                        <h5 class="text-white">Treefile</h5>
                        <i class="fas fa-book"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>


</div>
@endsection