<!-- Modal -->
<div class="modal fade" id="modal-sub-kriteria" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title-sub">Tambah Sub Kriteria untuk &nbsp;<span id="judul-kriteria"></span></h5>
                <button type="button" class="close btn-close" data-dismiss="modal" aria-label="Close" id="btn-close-sub">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <input type="hidden" id="kriteria_id" name="kriteria_id">
                    <div class="card-header">
                        <form action="javascript:;" class="d-inline" id="form-sub-kriteria">
                            <input type="hidden" name="current_unique" id="current_unique_sub">
                            @csrf
                            <div class="row">
                                <div class="col-sm-3">
                                    <input type="text" class="form-control" name="sub_kriteria" id="sub_kriteria" placeholder="Sub Kriteria">
                                </div>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control" name="nilai" id="nilai" placeholder="Nilai">
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" name="bobot" id="bobot" placeholder="Bobot">
                                </div>
                                <div class="col-sm-4" id="btn-action-add-sub"></div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <table id="table-sub-kriteria" class="table table-bordered table-hover dataTable dtr-inline">
                            <thead>
                                <th>No</th>
                                <th>Sub Kriteria</th>
                                <th>Nilai</th>
                                <th>bobot</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="btn-action-sub">
            </div>
        </div>
    </div>
</div>
