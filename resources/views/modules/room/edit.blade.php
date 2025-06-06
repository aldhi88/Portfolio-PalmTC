<form id="formEditModal"> @csrf @method('PUT')
    <input type="hidden" name="id">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="sampleModalLabel"><i class="feather icon-edit-1"></i> Change Data Form</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
                <div class="form-group">
                    <label><strong>Room Code</strong></label>
                    <input name="code" type="text" class="form-control form-control-sm">
                    <small><span class="code text-danger msg"></span></small>
                </div>
                <div class="form-group">
                    <label><strong>Room Name</strong></label>
                    <input name="name" type="text" class="form-control form-control-sm">
                    <small><span class="name text-danger msg"></span></small>
                </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-sm btn-primary">Save Changes</button>
        </div>
    </div>
</form>