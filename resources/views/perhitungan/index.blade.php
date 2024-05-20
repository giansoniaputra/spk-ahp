@extends('layouts.main')
@section('container')
<div class="row mb-2">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <button type="button" class="btn btn-primary" id="btn-add-perhitungan">Tambah Perhitungan</button>
            </div>
            <div class="card-body">
                <table id="table-perhitungan" class="table table-bordered table-hover dtr-inline" style="overflow:scroll ">
                    <thead>
                        <tr>
                            <th class="text-center" rowspan="2">Alternatif</th>
                            <th class="text-center" colspan="{{ getJumlahKriteria() }}">Kriteria</th>
                        </tr>
                        <tr>
                            @foreach ($kriteria as $item)
                            <th class="tetx-center">{{ $item['kriteria'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @if ($perhitungan->count('id') == 0)
                        <tr>
                            <td class="text-center" colspan="{{ 1 + getJumlahKriteria() }}">Belum Ada Perhitungan</td>
                        </tr>
                        @else

                        @foreach ($alternatif as $itemAlternatif)
                        <tr>
                            <td>{{ $itemAlternatif->alternatif .' - '. $itemAlternatif->keterangan }}</td>
                            @foreach($kriteria as $itemKriteria)
                            @php
                            $data = getDataPerhitungan($itemAlternatif['id'], $itemKriteria['id']);
                            @endphp
                            <td class="text-center" id="nilai-bobot">
                                <p class="p-bobot">{{ ($data->sub_kriteria_id == null) ? '' : $data['sub_kriteria']['bobot'].' - '.$data['sub_kriteria']['nilai'] }}</p>
                                <form action="javascript:;" id="form-update-bobot">
                                    @php
                                    $dataPilihan = getPilihanSubKriteria($itemKriteria['id']);
                                    @endphp
                                    <select name="" id="" class="d-none input-bobot" data-id="{{ $data->id }}" style="width:6vh">
                                        @foreach ($dataPilihan as $pilihan)
                                        <option value="{{ $pilihan['id'] }}" {{ ($data->sub_kriteria_id == $pilihan['id']) ? 'selected' : '' }}>{{ $pilihan['bobot'].' - '.$pilihan['nilai'] }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach

                        @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                @if($perhitungan->count('id') > 0)
                <button class="btn btn-primary float-right" id="btn-normalisasi">Lihat Hasil</button>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="card-body">
    <div id="normalisasi"></div>
    <div id="ranking"></div>
</div>
@endsection
@section('script')
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

                    // $("#spinner").html(loader)
                    $.ajax({
                        url: "/perhitungans/create"
                        , type: "GET"
                        , dataType: "json"
                        , success: function(response) {
                            console.log(response)
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
            let id = thiss.data("id")
            $.ajax({
                data: {
                    bobot: thiss.val()
                }
                , url: "/perhitungans/update/" + id
                , type: "get"
                , dataType: 'json'
                , success: function(response) {
                    console.log('OK')
                    console.log(response)
                    p.html(response.success)
                    thiss.val(response.success)
                }
            });
        });

        // KEPUTUSAN
        $("#btn-normalisasi").on("click", function() {
            $("#spinner").html(loader)
            $.ajax({
                url: "/normalisasi"
                , type: "GET"
                , dataType: 'json'
                , success: function(response) {
                    console.log(response)
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
                                            <th class="text-center" colspan="${data.sum_kriteria}">Kriteria</th>
                                        </tr>
                                        <tr>`;
                    data.kriteria.forEach((kriteria) => {
                        normalisasi += `<th class="tetx-center">${kriteria.kriteria}</th>`
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

                    // // PERANGKINGAN
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
                                                <td>Alternatif</td>
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
                                            <td>${a[2]}</td>
                                            <td>${a[1]}</td>
                                            <td>${b + 1}</td>
                                        </tr>
                                        `
                    })

                    ranking += `</tbody></table>`
                    rankingElement.innerHTML = ranking
                    $.ajax({
                        url: "/perhitunganSAW"
                        , type: "GET"
                        , dataType: 'json'
                        , success: function(response) {
                            let data = response.data
                            let rankingElement = document.querySelector('#ranking-saw')
                            let normalisasiElement = document.querySelector('#normalisasi-saw');
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
                                                <td>Alternatif</td>
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
                                            <td>A${b + 1}</td>
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
                    $("#spinner").html("")
                }
            });
        })
    });

</script>
@endsection
