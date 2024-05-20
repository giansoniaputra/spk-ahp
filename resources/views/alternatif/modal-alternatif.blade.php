<!-- Modal -->
<div class="modal fade" id="modal-alternatif" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn-close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="javascript:;" id="form-alternatif">
                    @csrf
                    <input type="hidden" name="uuid" id="current_uuid">
                    <div class="form-group mb-3">
                        <label for="alternatif">Alternatif</label>
                        <input type="text" id="alternatif" name="alternatif" class="form-control" placeholder="Masukan Alternatif">
                    </div>
                    <div class="form-group mb-3">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" class="form-control" id="keterangan" placeholder="Masukan keterangan"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer" id="btn-action">
            </div>
        </div>
    </div>
</div>
