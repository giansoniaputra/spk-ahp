<?php

use App\Models\Ir;
use App\Models\Kriteria;
use App\Models\Rangking;
use App\Models\Alternatif;
use App\Models\PvKriteria;
use App\Models\Perhitungan;
use App\Models\SubKriteria;
use Illuminate\Support\Str;
use App\Models\PvSubKriteria;
use Illuminate\Support\Facades\DB;
use App\Models\PerbandinganSubKriteria;
use App\Models\PerbandinganKriteriaModel;
use App\Http\Livewire\PerbandinganKriteria;

function getKriteriaID($urut)
{
    $data = DB::table('kriteria')->select('id')->orderBy('id')->get();

    return $data[$urut];
}

function cekPV()
{
    return Pvkriteria::first();
}
function cekAlternatif()
{
    return Alternatif::first();
}
function getAllKriteria()
{
    return Kriteria::all();
}
function cekPerbandinganSub()
{
    return PerbandinganSubKriteria::first();
}

function cekRanking()
{
    return Rangking::first();
}

function inputKriteriaPV($id_kriteria, $pv)
{
    $PvKriteria = PvKriteria::where('kriteria_id', $id_kriteria)->first();
    $data = [
        'kriteria_id' => $id_kriteria,
        'nilai' => $pv
    ];
    if ($PvKriteria) {
        $PvKriteria->fill($data);
        $PvKriteria->save();
    } else {
        PvKriteria::create($data);
    }
}

function inputSubKriteriaPV($id_sub_kriteria, $pv, $kriteria_id)
{
    $PvSubKriteria = PvSubKriteria::where('sub_kriteria_id', $id_sub_kriteria)->first();
    $data = [
        'sub_kriteria_id' => $id_sub_kriteria,
        'nilai' => $pv,
        'kriteria_id' => $kriteria_id
    ];
    if ($PvSubKriteria) {
        $PvSubKriteria->fill($data);
        $PvSubKriteria->save();
    } else {
        PvSubKriteria::create($data);
    }
}

function inputDataPerbandinganKriteria($kriteria1, $kriteria2, $value, $nilai)
{
    $kriteria1_id = getKriteriaID($kriteria1);
    $kriteria2_id = getKriteriaID($kriteria2);

    $dataPerbandinganKriteria = [
        'kriteria1_id' => $kriteria1_id->id,
        'kriteria2_id' => $kriteria2_id->id,
        'value' => $value,
        'nilai_perbandingan' => $nilai
    ];
    $perbandinganKriteria = PerbandinganKriteriaModel::where('kriteria1_id', $kriteria1_id->id)->where('kriteria2_id', $kriteria2_id->id)->first();

    if ($perbandinganKriteria) {
        $perbandinganKriteria->fill($dataPerbandinganKriteria);
        $perbandinganKriteria->save();
    } else {
        $dataPerbandinganKriteria['unique'] = Str::orderedUuid();
        PerbandinganKriteriaModel::create($dataPerbandinganKriteria);
    }
}

function getEigenVector($matrik_a, $matrik_b, $n)
{
    $eigenvektor = 0;
    for ($i = 0; $i <= ($n - 1); $i++) {
        $eigenvektor += ($matrik_a[$i] * (($matrik_b[$i]) / $n));
    }

    return $eigenvektor;
}
function getConsIndex($matrik_a, $matrik_b, $n)
{
    $eigenvektor = getEigenVector($matrik_a, $matrik_b, $n);
    $consindex = ($eigenvektor - $n) / ($n - 1);

    return $consindex;
}

// Mencari Consistency Ratio
function getConsRatio($matrik_a, $matrik_b, $n)
{
    $consindex = getConsIndex($matrik_a, $matrik_b, $n);
    $consratio = $consindex / getNilaiIR($n);

    return $consratio;
}

function getNilaiIR($jmlKriteria)
{
    $nilaiIR = Ir::where('jumlah', $jmlKriteria)->first();
    return $nilaiIR['nilai'];
}

function getKriteriaNama($no_urut)
{
    $nama = Kriteria::orderBy('id')->get('kriteria');

    return $nama[$no_urut];
}

function getSubKriteriaNama($no_urut, $kriteria_id)
{
    $nama = DB::table('sub_kriteria as a')
        ->join('alternatif as b', 'a.sub_kriteria', '=', 'b.id')
        ->select('a.*', 'b.alternatif')
        ->where('a.kriteria_id', $kriteria_id)->orderBy('a.id')->get('alternatif');

    return $nama[$no_urut];
}

function getNilaiPerbandinganKriteria($kriteria1, $kriteria2)
{
    $perbandinganKriteria = PerbandinganKriteriaModel::get();
    if ($perbandinganKriteria->count() > 0) {
        $id_kriteria1 = getKriteriaID($kriteria1);
        $id_kriteria2 = getKriteriaID($kriteria2);

        $data = PerbandinganKriteriaModel::where('kriteria1_id', $id_kriteria1->id)->where('kriteria2_id', $id_kriteria2->id)->first();

        if ($data['value'] == 1) {
            $nilai = $data['nilai_perbandingan'];
        } else {
            $nilai = 1 / ($data['nilai_perbandingan']);
        }


        return round($nilai);
    }

    return '';
}

function getNilaiPerbandinganSubKriteria($sub_kriteria1, $sub_kriteria2, $kriteria_id)
{

    $perbandinganSubKriteria = PerbandinganSubKriteria::where('kriteria_id', $kriteria_id)->get();

    if ($perbandinganSubKriteria->count() > 0) {
        $id_sub_kriteria1 = getSubKriteriaID($sub_kriteria1, $kriteria_id);
        $id_sub_kriteria2 = getSubKriteriaID($sub_kriteria2, $kriteria_id);

        $data = PerbandinganSubKriteria::where('sub_kriteria1_id', $id_sub_kriteria1->id)->where('sub_kriteria2_id', $id_sub_kriteria2->id)->first();

        if ($data['value'] == 1) {
            $nilai = $data['nilai_perbandingan'];
        } else {
            $nilai = 1 / ($data['nilai_perbandingan']);
        }


        return round($nilai);
    }
    return '';
}

function getChecked($kriteria1, $kriteria2)
{
    $perbandinganKriteria = PerbandinganKriteriaModel::get();
    if ($perbandinganKriteria->count() > 0) {
        $id_kriteria1 = getKriteriaID($kriteria1);
        $id_kriteria2 = getKriteriaID($kriteria2);

        $data = PerbandinganKriteriaModel::where('kriteria1_id', $id_kriteria1->id)->where('kriteria2_id', $id_kriteria2->id)->first();

        return $data['value'];
    }

    return 1;
}

function getCheckedSubkriteria($sub_kriteria1, $sub_kriteria2, $kriteria_id)
{
    $perbandinganSubKriteria = PerbandinganSubKriteria::where('kriteria_id', $kriteria_id)->get();

    if ($perbandinganSubKriteria->count() > 0) {
        $id_sub_kriteria1 = getSubKriteriaID($sub_kriteria1, $kriteria_id);
        $id_sub_kriteria2 = getSubKriteriaID($sub_kriteria2, $kriteria_id);

        $data = PerbandinganSubKriteria::where('sub_kriteria1_id', $id_sub_kriteria1->id)->where('sub_kriteria2_id', $id_sub_kriteria2->id)->first();

        return $data['value'];
    }
    return 1;
}

function getSubKriteriaID($urut, $kriteria_id)
{
    $data = DB::table('sub_kriteria')->where('kriteria_id', $kriteria_id)->select('id')->orderBy('id')->get();

    return $data[$urut];
}

function getAlternatifID($urut)
{
    $data = DB::table('alternatif')->select('id')->orderBy('id')->get();

    return $data[$urut];
}

// mencari jumlah kriteria
function getJumlahKriteria()
{
    $data = Kriteria::count();

    return $data;
}

function getJumlahSubkriteria($kriteria_id)
{
    $data = SubKriteria::where('kriteria_id', $kriteria_id)->count();

    return $data;
}

// menampilkan tabel perbandingan bobot
function showTabelPerbandinganSubkriteria($jenis, $kriteria_id)
{
    $n = getJumlahSubkriteria($kriteria_id);

    $SubKriteria = DB::table('sub_kriteria as a')
        ->join('alternatif as b', 'a.sub_kriteria', '=', 'b.id')
        ->select('a.*', 'b.alternatif')
        ->where('a.kriteria_id', $kriteria_id)->get();
    foreach ($SubKriteria as $row) {
        $pilihan[] = $row->alternatif;
    }

    $data = [
        'n' => $n,
        'pilihan' => $pilihan,
        'jenis' => $jenis,
        'kriteria_id' => $kriteria_id
    ];
    return view('perbandingan-subkriteria.form-data', $data);
}

function inputDataPerbandinganSubKriteria($subkriteria1, $subkriteria2, $value, $nilai, $kriteria_id)
{
    $subkriteria1_id = getSubKriteriaID($subkriteria1, $kriteria_id)->id;
    $subkriteria2_id = getSubKriteriaID($subkriteria2, $kriteria_id)->id;

    $dataPerbandinganSubKriteria = [
        'sub_kriteria1_id' => $subkriteria1_id,
        'sub_kriteria2_id' => $subkriteria2_id,
        'value' => $value,
        'nilai_perbandingan' => $nilai,
        'kriteria_id' => $kriteria_id
    ];
    $perbandinganSubKriteria = PerbandinganSubKriteria::where('sub_kriteria1_id', $subkriteria1_id)->where('sub_kriteria2_id', $subkriteria2_id)->first();

    if ($perbandinganSubKriteria) {
        $perbandinganSubKriteria->fill($dataPerbandinganSubKriteria);
        $perbandinganSubKriteria->save();
    } else {
        $dataPerbandinganSubKriteria['unique'] = Str::orderedUuid();
        PerbandinganSubKriteria::create($dataPerbandinganSubKriteria);
    }
}

function getDataPerhitungan($alterinatif_id, $kriteria_id)
{
    return Perhitungan::with('sub_kriteria')->where('alternatif_id', $alterinatif_id)->where('kriteria_id', $kriteria_id)->first();
}

function getPilihanSubKriteria($kriteria_id)
{
    return SubKriteria::where('kriteria_id', $kriteria_id)->get();
}

// mencari jumlah alternatif
function getJumlahAlternatif()
{
    $data = Alternatif::count();

    return $data;
}
function getNilaiPvPerhitungan($alternatif_id, $kriteria_id)
{
    $data = Perhitungan::where('alternatif_id', $alternatif_id)->where('kriteria_id', $kriteria_id)->first();

    $dataPv = PvSubKriteria::where('sub_kriteria_id', $data['sub_kriteria_id'])->first();

    return $dataPv['nilai'];
}

function getNilaiPvKriteria($kriteria_id)
{
    $dataPv = PvKriteria::where('kriteria_id', $kriteria_id)->first();

    return $dataPv['nilai'];
}
