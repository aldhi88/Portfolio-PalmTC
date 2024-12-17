<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>

<script>

var dtTable = $('#myTable').DataTable({
        processing: true,serverSide: true,scrollX: true,pageLength: 25,
        order: [[0, 'desc'],],
        columnDefs: [
            { className: 'text-center', targets: ['_all'] },
        ],
        ajax: '{{ route("rooting-lists.dt") }}',
        columns: [
            { data: 'sample_number_format', name: 'tc_samples.sample_number', orderable:true, searchable:true},
            { data: 'tc_samples.program', name: 'tc_samples.program', orderable:true, searchable:true},
            { data: 'column1', name: 'column1', orderable:false, searchable:false},
            { data: 'explant1', name: 'explant1', orderable:false, searchable:false},
            { data: 'column2', name: 'column2', orderable:false, searchable:false},
            { data: 'total_bottle_active', name: 'total_bottle_active', orderable:false, searchable:false},
            { data: 'total_leaf_active', name: 'total_leaf_active', orderable:false, searchable:false},
        ],
        initComplete: function () {
            $('#header-filter th').each(function() {
                var title = $(this).text();
                var disable = $(this).attr("disable");
                if(disable!="true"){
                    $(this).html('<input placeholder="'+title+'" type="text" class="form-control column-search px-1 form-control-sm"/>');
                }
            });
            $('#header-filter').on('keyup', ".column-search",function () {
                dtTable
                    .column( $(this).parent().index() )
                    .search( this.value )
                    .draw();
            });
        }
    });


</script>