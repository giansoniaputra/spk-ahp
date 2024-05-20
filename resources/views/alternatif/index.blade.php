@extends('layouts.main')
@section('container')
<div class="row mb-2">
    <div class="col">
        <button type="button" class="btn btn-primary" id="btn-add-data">Tambah Alternatif</button>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
            </div>
            <div class="card-body">
                <table id="table-alternatif" class="table table-bordered table-hover dataTable dtr-inline">
                    <thead>
                        <th>Kode</th>
                        <th>Alternatif</th>
                        <th>Keterangan</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@include('/alternatif.modal-alternatif')
@endsection
@section('script')
<script>
    $(document).ready(function() {
        let table = $("#table-alternatif").DataTable({
            responsive: true
            , responsive: !0
            , autoWidth: false
            , serverSide: true
            , ajax: {
                url: "/dataTablesAlternatifs"
            , }
            , columns: [{
                    data: null
                    , orderable: false
                    , render: function(data, type, row, meta) {
                        var pageInfo = $("#table-alternatif").DataTable().page.info();
                        var index = meta.row + pageInfo.start + 1;
                        return index;
                    }
                , }
                , {
                    data: "alternatif"
                , }
                , {
                    data: "keterangan"
                , }
                , {
                    data: "action"
                    , orderable: true
                    , searchable: true
                , }
            , ]
            , columnDefs: [{
                    targets: [3], // index kolom atau sel yang ingin diatur
                    className: "text-center", // kelas CSS untuk memposisikan isi ke tengah
                }
                , {
                    searchable: false
                    , orderable: false
                    , targets: 0, // Kolom nomor, dimulai dari 0
                }
            , ]
        , });

        // Ketika Tombol Tambah Di Klik
        $("#btn-add-data").on("click", function() {
            $("#modal-alternatif").modal("show");
            $("#modal-title").html("Tambah Data Alternatif")
            $("#btn-action").html(`
            <button class="btn btn-primary" id="btn-save">Tambah</button>
        `);
        });

        function reset() {
            let form = $("form[id='form-alternatif']").serializeArray();
            form.map((a) => {
                $(`#${a.name}`).val("");
            });

            $("#btn-action").html("")
            $("#modal-title").html("")
        }

        // Ketika tombol close modal di tekan, dilakukan reset data
        $(".btn-close").on("click", function() {
            reset();
        })

        $("#modal-alternatif").on("click", "#btn-save", function() {
            let form = $("form[id='form-alternatif']").serialize();
            $.ajax({
                data: form
                , url: "/alternatifs"
                , type: "POST"
                , dataType: 'json'
                , success: function(response) {
                    if (response.errors) {
                        displayErrors(response.errors);
                    } else {
                        table.ajax.reload()
                        reset()
                        $("#modal-alternatif").modal("hide");
                        Swal.fire("Success!", response.success, "success");
                    }
                }
            });
        });

        $("#table-alternatif").on("click", ".edit-button", function() {
            let unique = $(this).data("unique");
            $("#current_uuid").val(unique)
            $.ajax({
                url: "/alternatifs/" + unique + "/edit"
                , type: "GET"
                , dataType: 'json'
                , success: function(response) {
                    $("#modal-title").html("Ubah Data Alternatif")
                    $("#btn-action").html(`
                    <button class="btn btn-primary" id="btn-update">Ubah</button>
                `)
                    $("#alternatif").val(response.data.alternatif)
                    $("#keterangan").val(response.data.keterangan)
                    $("#modal-alternatif").modal("show");
                }
            });
        });

        $("#modal-alternatif").on("click", "#btn-update", function() {
            let form = $("form[id='form-alternatif']").serialize();
            $.ajax({
                data: form + "&_method=PUT"
                , url: "/alternatifs/" + $("#current_uuid").val()
                , type: "POST"
                , dataType: 'json'
                , success: function(response) {
                    if (response.errors) {
                        displayErrors(response.errors);
                    } else {
                        table.ajax.reload()
                        reset()
                        $("#modal-alternatif").modal("hide");
                        Swal.fire("Success!", response.success, "success");
                    }
                }
            });
        })
        //HAPUS DATA
        $("#table-alternatif").on("click", ".delete-button", function() {
            let unique = $(this).attr("data-unique");
            let token = $(this).attr("data-token");
            Swal.fire({
                title: "Apakah Kamu Yakin?"
                , text: "Kamu akan menghapus data ini!"
                , icon: "warning"
                , showCancelButton: true
                , confirmButtonColor: "#3085d6"
                , cancelButtonColor: "#d33"
                , confirmButtonText: "Yes, Hapus!"
            , }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        data: {
                            _method: "DELETE"
                            , _token: token
                        , }
                        , url: "/alternatifs/" + unique
                        , type: "POST"
                        , dataType: "json"
                        , success: function(response) {
                            table.ajax.reload();
                            Swal.fire("Deleted!", response.success, "success");
                        }
                    , });
                }
            });
        });
    });

</script>
@endsection
