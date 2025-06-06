<script>
    function generateDataDelete(id){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: false,
            url: '{{ url("plantations/get-data") }}/'+id,
            success: (a) => {
                var data = a.data.data[0];
                $('#formDeleteModal #attr-data').text('').text(data.name);
                $('#formDeleteModal input[name="id"]').val(data.id);
            },
            error: (a) => {
                alert("Error #008, please contact your admin.");
            }
        });
    }
    $("#deleteModal").on("show.bs.modal", function(e) {
        var id = $(e.relatedTarget).data('id');
        generateDataDelete(id);
    });
    $('#formDeleteModal').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        var key = $("#formDeleteModal input[name='id']").val();
        $.ajax({
            type: 'POST', cache: false, contentType: false, processData: false,
            url: "{{ url('plantations') }}/"+key,
            data: formData,
            success: (a) => {
                if(a.status == 'success'){
                    dtTable.ajax.reload();
                }
                $('#deleteModal').modal('toggle');
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                loader(false);
            },
            error: (a) => {
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                loader(false);
            }
        });
    });
</script>

