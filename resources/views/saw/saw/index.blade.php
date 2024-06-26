@extends('layouts.main')
@section('container')
<div class="row mb-2">
    <div class="col">
        <button type="button" class="btn btn-primary" id="btn-add-perhitungan">Tambah Perhitungan SAW</button>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
            </div>
            <div class="card-body">
                <table id="table-perhitungan" class="table table-bordered table-hover dtr-inline" style="overflow:scroll ">
                    <thead>
                        <tr>
                            <th class="text-center" rowspan="2">Alternatif</th>
                            <th class="text-center" rowspan="2">Keterangan</th>
                            <th class="text-center" colspan="{{ $sum_kriteria }}">Kriteria</th>
                        </tr>
                        <tr>
                            @foreach ($kriterias as $row)
                            <th class="tetx-center">{{ "C".$row->kode }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @if($perhitungan->count('a.id') == 0)
                        <tr>
                            <td class="text-center" colspan="{{ 2 + $sum_kriteria }}">Belum Ada Perhitungan</td>
                        </tr>
                        @else
                        @foreach ($alternatifs as $alternatif)
                        <tr>
                            <td>A{{ $alternatif->alternatif }}</td>
                            <td>{{ $alternatif->keterangan }}</td>
                            @foreach($kriterias as $kriteria)
                            @php
                            $bobots = DB::table('perhitungan_s_a_w_s')
                            ->where('kriteria_uuid', $kriteria->uuid)
                            ->where('alternatif_uuid', $alternatif->uuid)
                            ->get();
                            @endphp
                            @foreach($bobots as $bobot)
                            <td class="text-center" id="nilai-bobot">
                                <p class="p-bobot">{{ $bobot->bobot }}</p>
                                <form action="javascript:;" id="form-update-bobot">
                                    <input type="number" class="d-none input-bobot" data-uuid={{ $bobot->uuid }} value="{{ $bobot->bobot }}" style="width:6vh">
                                </form>
                            </td>
                            @endforeach
                            @endforeach
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-center">Nilai Bobot(Wj)</th>
                            @foreach ($kriterias as $kriteria)
                            <th>{{ $kriteria->bobot }}%</th>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                @if($perhitungan->count('a.id') > 0)
                <button class="btn btn-primary float-right" id="btn-normalisasi">Cari Keputusan</button>
                @endif
            </div>
        </div>
    </div>
</div>
<div id="normalisasi"></div>
<div id="ranking"></div>
<script>
    $(document).ready(function() {
        $("#btn-add-perhitungan").on("click", function() {
            Swal.fire({
                title: "Yakin ingin menambah data baru?"
                , text: "Anda akan mereset data sebelumnya"
                , icon: "warning"
                , showCancelButton: true
                , confirmButtonColor: "#3085d6"
                , cancelButtonColor: "#d33"
                , confirmButtonText: "Yes, Buat!"
            , }).then((result) => {
                if (result.isConfirmed) {
                    $("#spinner").html(loader)
                    $.ajax({
                        url: "/perhitungan-create"
                        , type: "GET"
                        , dataType: "json"
                        , success: function(response) {
                            $("#spinner").html("")
                            Swal.fire({
                                position: "center"
                                , icon: "success"
                                , title: response.success
                                , showConfirmButton: false
                                , timer: 1500
                            });
                            setTimeout(() => {
                                document.location.reload();
                            }, 2000)
                        }
                    , });
                }
            });
        });
        $("#table-perhitungan").on("click", "#nilai-bobot", function() {
            let current_input = document.querySelectorAll(".input-bobot")
            current_input.forEach((a) => {
                a.classList.add('d-none')
                a.parentElement.previousElementSibling.classList.remove('d-none')
            })
            $(this).children().eq(0).addClass("d-none")
            $(this).children().eq(1).children().eq(0).removeClass("d-none")
            $(this).children().eq(1).children().eq(0).focus()
        })
        $("#table-perhitungan").on("change", ".input-bobot", function() {
            let thiss = $(this)
            let p = $(this).parent().prev()
            let uuid = thiss.data("uuid")
            $.ajax({
                data: {
                    bobot: thiss.val()
                }
                , url: "/perhitungan-update/" + uuid
                , type: "get"
                , dataType: 'json'
                , success: function(response) {
                    p.html(response.success)
                    thiss.val(response.success)
                }
            });
        })

        // KEPUTUSAN
        $("#btn-normalisasi").on("click", function() {
            $("#spinner").html(loader)
            $.ajax({
                url: "/saw-normalisasi"
                , type: "GET"
                , dataType: 'json'
                , success: function(response) {
                    let data = response.data
                    let rankingElement = document.querySelector('#ranking')
                    let normalisasiElement = document.querySelector('#normalisasi');
                    let keys = Object.keys(data.perhitungan)
                    let normalisasi = `
                <div class="row mt-3">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                            <h3>Tabel Normalisasi</h3>
                            </div>
                            <div class="card-body">
                                <table id="table-normalisasi" class="table table-bordered table-hover dtr-inline" style="overflow:scroll ">
                                    <thead>
                                        <tr>
                                            <th class="text-center" rowspan="2">Alternatif</th>
                                            <th class="text-center" rowspan="2">Keterangan</th>
                                            <th class="text-center" colspan="${data.sum_kriteria}">Kriteria</th>
                                        </tr>
                                        <tr>`;
                    data.kriterias.forEach((kriteria) => {
                        normalisasi += `<th class="tetx-center">C${kriteria.kode}</th>`
                    })
                    normalisasi += `
                </tr>
                </thead>
                <tbody>
                `;
                    if (keys.length == 0) {
                        normalisasi += `<tr><td class="text-center" colspan="${2 + data.sum_kriteria}">Belum Ada Perhitungan</td></tr>`
                    } else {
                        normalisasi += data.elements
                    }
                    normalisasiElement.innerHTML = normalisasi

                    // PERANGKINGAN
                    let ranking = `
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                <h3>Tabel Perankingan</h3>
                                </div>
                                <div class="card-body">
                                    <table id="table-perankingan" class="table table-bordered table-hover dtr-inline" style="overflow:scroll ">
                                        <thead>
                                            <tr>
                                                <td>Keterangan</td>
                                                <td>Bobot</td>
                                                <td>Ranking</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                    `;
                    data.ranking.forEach((a, b) => {
                        ranking += `
                                        <tr>
                                            <td>${a[0]}</td>
                                            <td>${a[1]}</td>
                                            <td>${b + 1}</td>
                                        </tr>
                                        `
                    })

                    ranking += `</tbody></table>`
                    rankingElement.innerHTML = ranking
                    $("#spinner").html("")
                }
            });
        })
        // WASPAS
        $("#btn-waspas").on("click", function() {
            $("#spinner").html(loader)
            $.ajax({
                url: "/waspas-normalisasi"
                , type: "GET"
                , dataType: 'json'
                , success: function(response) {
                    let data = response.data
                    console.log(data);
                    let rankingElement = document.querySelector('#ranking')
                    let normalisasiElement = document.querySelector('#normalisasi');
                    let keys = Object.keys(data.perhitungan)
                    let normalisasi = `
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                <h3>Tabel Normalisasi</h3>
                                </div>
                                <div class="card-body">
                                    <table id="table-normalisasi" class="table table-bordered table-hover dtr-inline" style="overflow:scroll ">
                                        <thead>
                                            <tr>
                                                <th class="text-center" rowspan="2">Alternatif</th>
                                                <th class="text-center" rowspan="2">Keterangan</th>
                                                <th class="text-center" colspan="${data.sum_kriteria}">Kriteria</th>
                                            </tr>
                                            <tr>`;
                    data.kriterias.forEach((kriteria) => {
                        normalisasi += `<th class="tetx-center">C${kriteria.kode}</th>`
                    })
                    normalisasi += `
                    </tr>
                    </thead>
                    <tbody>
                    `;
                    if (keys.length == 0) {
                        normalisasi += `<tr><td class="text-center" colspan="${2 + data.sum_kriteria}">Belum Ada Perhitungan</td></tr>`
                    } else {
                        normalisasi += data.elements
                    }
                    normalisasiElement.innerHTML = normalisasi

                    // PERANGKINGAN
                    let ranking = `
                        <div class="row mt-3">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                    <h3>Tabel Perankingan</h3>
                                    </div>
                                    <div class="card-body">
                                        <table id="table-perankingan" class="table table-bordered table-hover dtr-inline" style="overflow:scroll ">
                                            <thead>
                                                <tr>
                                                    <td>Keterangan</td>
                                                    <td>Botot</td>
                                                    <td>Ranking</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                        `;
                    data.ranking.forEach((a, b) => {
                        ranking += `
                                            <tr>
                                                <td>${a[0]}</td>
                                                <td>${a[1]}</td>
                                                <td>${b + 1}</td>
                                            </tr>
                                            `
                    })

                    ranking += `</tbody></table>`
                    rankingElement.innerHTML = ranking
                    $("#spinner").html("")
                }
            });
        })
        // Fungsi untuk mentranspose matriks
        function transpose(matrix) {
            return matrix[0].map((col, i) => matrix.map(row => row[i]));
        }
    })

</script>
@endsection
