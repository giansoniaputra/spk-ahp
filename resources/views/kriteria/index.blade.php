@extends('layouts.main')
@section('container')

<div class="row mb-2">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <button type="button" class="btn btn-primary" id="btn-add-data">Tambah Kriteria</button>
            </div>
            <div class="card-body">
                <table id="table-kriteria" class="table table-bordered table-hover dataTable dtr-inline">
                    <thead>
                        <th width="5%">No</th>
                        <th>Kriteria</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@include('kriteria.modal-kriteria')
@include('kriteria.modal-sub-kriteria')
@endsection

@section('script')
<script>
    $(document).ready(function() {
        let table = $("#table-kriteria").DataTable({
            responsive: true
            , responsive: !0
            , autoWidth: false
            , serverSide: true
            , ajax: {
                url: "/dataTablesKriterias"
            , }
            , columns: [{
                    data: null
                    , render: function(data, type, row, meta) {
                        var pageInfo = $("#table-kriteria").DataTable().page.info();
                        var index = meta.row + pageInfo.start + 1;
                        return index;
                    }
                    , orderable: false
                    , searchable: false
                , }
                , {
                    data: "kriteria"
                , }
                , {
                    data: "action"
                    , orderable: false
                    , searchable: false
                , }
            , ]
            , columnDefs: [{
                    targets: [2], // index kolom atau sel yang ingin diatur
                    className: "text-center", // kelas CSS untuk memposisikan isi ke tengah
                }
                , {
                    searchable: false
                    , orderable: false
                    , targets: 0, // Kolom nomor, dimulai dari 0
                }
            , ]
        , });

        // KETIKA BUTTON TAMBAH DATA DI KLIK
        $("#btn-add-data").on("click", function() {
            $("#modal-title").html('Tambah Kriteria')
            $("#btn-modal-action").html(`<button type="button" class="btn btn-primary" id="btn-save">Tambah</button>`)
            $("#modal-kriteria").modal("show");
        })

        // Mereset SEluruh data di form modal
        function reset() {
            let form = $("form[id='form-kriteria']").serializeArray();
            form.map((a) => {
                $(`#${a.name}`).val("");
            })

            let formSub = $("form[id='form-sub-kriteria']").serializeArray();
            formSub.map((a) => {
                $(`#${a.name}`).val("");
            })


            $("#btn-modal-action").html("")
            $("#modal-title").html("")
        }

        // Ketika tombol close modal di tekan, dilakukan reset data
        $(".btn-close").on("click", function() {
            reset();
        })

        // Proses Penyimpanan data kriteria
        $("#modal-kriteria").on("click", "#btn-save", function() {
            let button = $(this)
            button.attr('disabled', "true");
            $.ajax({
                data: $("form[id='form-kriteria']").serialize()
                , url: "/kriterias"
                , type: "POST"
                , dataType: 'json'
                , success: function(response) {
                    if (response.errors) {
                        displayErrors(response.errors);
                        button.removeAttr('disabled');
                    } else {
                        table.ajax.reload()
                        button.removeAttr('disabled');
                        reset()
                        $("#modal-kriteria").modal("hide");
                        Swal.fire("Success!", response.success, "success");
                    }
                }
            });
        });

        // AMBIL DATA Kriteria
        $("#table-kriteria").on("click", ".edit-button", function() {
            let unique = $(this).data("unique");
            $.ajax({
                url: "/kriterias/" + unique + "/edit"
                , type: "GET"
                , dataType: 'json'
                , success: function(response) {
                    data = response.data
                    $("#current_unique").val(data.unique);
                    $("#kriteria").val(data.kriteria);
                    $("#btn-modal-action").html(`<button class="btn btn-primary" id="update-button">Update</button>`)
                    $("#modal-title").html("Edit Data Kriteria");
                    $("#modal-kriteria").modal("show");
                }
            });
        });

        // Update Data Kriteria
        // UPDATE DATA
        $("#modal-kriteria").on("click", "#update-button", function() {
            let button = $(this)
            $(button).attr("disabled", "true");
            let form = $("form[id='form-kriteria']").serialize();
            $.ajax({
                data: form + "&_method=PUT"
                , url: "/kriterias/" + $("#current_unique").val()
                , type: "POST"
                , dataType: 'json'
                , success: function(response) {
                    if (response.errors) {
                        $(button).removeAttr("disabled");
                        displayErrors(response.errors);
                    } else {
                        table.ajax.reload()
                        $(button).removeAttr("disabled");
                        reset();
                        $("#modal-kriteria").modal("hide");
                        Swal.fire("Success!", response.success, "success");
                    }
                }
            });
        });

        //HAPUS DATA
        $("#table-kriteria").on("click", ".delete-button", function() {
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
                        , url: "/kriterias/" + unique
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

        let table2 = $("#table-sub-kriteria").DataTable({
            responsive: true,
            // responsive: !0,
            autoWidth: false
            , serverSide: true
            , ajax: {
                url: "/dataTablesSubKriterias"
                , type: "GET"
                , dataType: "json"
                , data: function(d) {
                    d.kriteria_id = $("#kriteria_id").val()
                }
            }
            , columns: [{
                    data: null
                    , orderable: false
                    , render: function(data, type, row, meta) {
                        var pageInfo = $("#table-sub-kriteria").DataTable().page.info();
                        var index = meta.row + pageInfo.start + 1;
                        return index;
                    }
                , }
                , {
                    data: "sub_kriteria"
                , }
                , {
                    data: "nilai"
                , }
                , {
                    data: "bobot"
                , }
                , {
                    data: "action"
                    , orderable: true
                    , searchable: true
                , }
            , ]
            , columnDefs: [{
                targets: [4], // index kolom atau sel yang ingin diatur
                className: "text-center", // kelas CSS untuk memposisikan isi ke tengah
            }, ]
        , });


        // KETIKA TOMBOL SUBKRITERIA DI KLIK
        $("#table-kriteria").on("click", ".sub-button", function() {
            $("#judul-kriteria").html($(this).data('judul'))
            $("#btn-action-add-sub").html(`<button class="btn btn-primary btn-md" id="btn-add-sub">Tambah Sub Kriteria</button>`)
            $("#kriteria_id").val($(this).data('kriteria_id'))
            table2.ajax.reload()
            $("#modal-sub-kriteria").modal("show")
        })

        // KETIKA TOMBOL TAMBAH SUB DI KLIK
        $("#modal-sub-kriteria").on("click", "#btn-add-sub", function() {
            $.ajax({
                data: $('form[id="form-sub-kriteria"]').serialize() + '&kriteria_id=' + $("#kriteria_id").val()
                , url: "/subkriterias"
                , type: "POST"
                , dataType: 'json'
                , success: function(response) {
                    if (response.errors) {
                        displayErrors(response.errors)
                    } else {
                        reset()
                        table2.ajax.reload()
                    }
                }
            });
        });

        // AMBIL DATA YANG AKAN DI EDIT
        $("#table-sub-kriteria").on("click", ".edit-button", function() {
            let unique = $(this).data("unique");
            $("#current_unique_sub").val(unique)
            $.ajax({
                url: "/subkriterias/" + unique + "/edit"
                , type: "GET"
                , dataType: 'json'
                , success: function(response) {
                    $("#sub_kriteria").focus()
                    $("#sub_kriteria").val(response.data.sub_kriteria);
                    $("#nilai").val(response.data.nilai);
                    $("#bobot").val(response.data.bobot);
                    $("#btn-action-add-sub").html(`<button class="btn btn-warning text-white btn-md mr-2" id="btn-update-sub">Update Sub Kriteria</button><button class="btn btn-danger btn-md" id="btn-batal-update"><i class="far fa-times-circle"></i></button>`)

                }
            });
        });

        // UPDATE SUB KRITERIA
        $("#modal-sub-kriteria").on("click", "#btn-update-sub", function() {
            let form = $("#form-sub-kriteria").serialize();
            $.ajax({
                data: form + '&_method=PUT'
                , url: "/subkriterias/" + $("#current_unique_sub").val()
                , type: "POST"
                , dataType: 'json'
                , success: function(response) {
                    $("#btn-action-add-sub").html(`<button class="btn btn-primary btn-md" id="btn-add-sub">Tambah Sub Kriteria</button>`)
                    reset();
                    table2.ajax.reload();
                }
            });
        })

        //HAPUS DATA
        $("#table-sub-kriteria").on("click", ".delete-button", function() {
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
                        , url: "/subkriterias/" + unique
                        , type: "POST"
                        , dataType: "json"
                        , success: function(response) {
                            table2.ajax.reload();
                        }
                    , });
                }
            });
        });

    });

</script>
@endsection
