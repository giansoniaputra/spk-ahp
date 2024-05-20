<!-- Modal -->
<div class="modal fade" id="modal-kriteria" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title"></h5>
                <button type="button" class="close btn-close" data-dismiss="modal" aria-label="Close" id="">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="uuid">
                <form action="javascript:;" id="form-kriteria">
                    @csrf
                    <input type="hidden" name="current_unique" id="current_unique">
                    <div class="form-group mb-3">
                        <label for="kriteria">Kriteria</label>
                        <input type="text" id="kriteria" name="kriteria" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer" id="btn-modal-action">
            </div>
        </div>
    </div>
</div>
