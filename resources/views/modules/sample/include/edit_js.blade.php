<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/moment.js/moment.min.js')}}"></script>
@include('modules.sample.include.treefile_js')
@include('modules.sample.include.sample_js')
<script>
    getWeek();
    function getWeek(){
        var date = $('#formEditModal input[name="created_at"]').val();
        var weekNumb = moment(date, "YYYYMMDD").isoWeek();
        $('#formEditModal input[name="week"]').val(weekNumb);
    }
    $('#formEditModal input[name="created_at"]').on('change', function(){
        getWeek();
    })

    $('#formEditModal').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        var key = $("#formEditModal input[name='id']").val();
        $.ajax({
            type: 'POST',
            url: '{{ url("samples") }}/'+key,
            data: formData,
            cache: false, contentType: false, processData: false,
            success: (a) => {
                // $('#formEditModal').trigger('reset');
                getWeek();
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                loader(false);
            },
            error: (a) => {
                if(a.status == 422){
                    resetValidationEdit();
                    $.each(a.responseJSON.errors, function(key, value){
                        cekValidationEdit(key, value);
                    })
                }else{
                    showAlert('danger', 'times', 'alert-area', a.status);
                    $('#edit').modal('toggle');
                }

                loader(false);
            }
        });
    });

    $('input[name="program"]').keypress(function (e) {
        var regex = new RegExp("^[a-zA-Z0-9]|[-]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }
        e.preventDefault();
        return false;
    });
</script>

