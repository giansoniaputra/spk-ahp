<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\PerbandinganSubKriteria;
use App\Models\PvSubKriteria;
use App\Models\SubKriteria;
use Illuminate\Http\Request;
use Symfony\Component\Console\Input\Input;

class PerbandinganSubkriteriaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->reset && $request->reset == 1) {
            PerbandinganSubKriteria::truncate();
        }
        $jenis = $request->input('data');
        $data = [
            'title' => 'Perbandingan Alternatif',
            'kriteria' => Kriteria::with('sub_kriteria')->get(),
            'jenis' => $jenis

        ];
        // return response()->json($request);
        return view('livewire.perbandingan-subkriteria-livewire', $data);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $n = count(SubKriteria::where('kriteria_id', $data['kriteria_id'])->get());
        $matrik = array();
        $urut     = 0;
        for ($x = 0; $x <= ($n - 2); $x++) {
            for ($y = ($x + 1); $y <= ($n - 1); $y++) {
                $urut++;
                $pilih    = "pilih" . $urut;
                $bobot     = "bobot" . $urut;
                if ($data[$pilih] == 1) {
                    $matrik[$x][$y] = $data[$bobot];
                    $matrik[$y][$x] = 1 / $data[$bobot];
                } else {
                    $matrik[$x][$y] = 1 / $data[$bobot];
                    $matrik[$y][$x] = $data[$bobot];
                }

                inputDataPerbandinganSubKriteria($x, $y, $data[$pilih], $matrik[$x][$y], $data['kriteria_id']);
            }
        }

        // diagonal --> bernilai 1
        for ($i = 0; $i <= ($n - 1); $i++) {
            $matrik[$i][$i] = 1;
        }

        // inisialisasi jumlah tiap kolom dan baris kriteria
        $jmlmpb = array();
        $jmlmnk = array();
        for ($i = 0; $i <= ($n - 1); $i++) {
            $jmlmpb[$i] = 0;
            $jmlmnk[$i] = 0;
        }

        // menghitung jumlah pada kolom kriteria tabel perbandingan berpasangan
        for ($x = 0; $x <= ($n - 1); $x++) {
            for ($y = 0; $y <= ($n - 1); $y++) {
                $value        = $matrik[$x][$y];
                $jmlmpb[$y] += $value;
            }
        }

        // menghitung jumlah pada baris kriteria tabel nilai kriteria
        // matrikb merupakan matrik yang telah dinormalisasi
        for ($x = 0; $x <= ($n - 1); $x++) {
            for ($y = 0; $y <= ($n - 1); $y++) {
                $matrikb[$x][$y] = $matrik[$x][$y] / $jmlmpb[$y];
                $value    = $matrikb[$x][$y];
                $jmlmnk[$x] += $value;
            }

            // nilai priority vektor
            $pv[$x]     = $jmlmnk[$x] / $n;


            $id_sub_kriteria = getSubKriteriaID($x, $data['kriteria_id']);
            inputSubKriteriaPV($id_sub_kriteria->id, $pv[$x], $data['kriteria_id']);
            // else {
            //     $id_kriteria    = getKriteriaID($jenis - 1);
            //     $id_alternatif    = getAlternatifID($x);
            //     inputAlternatifPV($id_alternatif, $id_kriteria->id, $pv[$x]);
            // }
        }

        $eigenvektor = getEigenVector($jmlmpb, $jmlmnk, $n);
        $consIndex   = getConsIndex($jmlmpb, $jmlmnk, $n);
        $consRatio   = getConsRatio($jmlmpb, $jmlmnk, $n);

        $dataView = [
            'title' => 'Perbandingan Kriteria',
            'matrik' => $matrik,
            'jmlmpb' => $jmlmpb,
            'matrikb' => $matrikb,
            'jmlmnk' => $jmlmnk,
            'pv' => $pv,
            'eigenvektor' => $eigenvektor,
            'consIndex' => $consIndex,
            'consRatio' => $consRatio,
            'n' => $n,
            'kriteria_id' => $data['kriteria_id'],
            'jenis' => $data['jenis']

        ];

        // return response()->json($dataView);
        return view('perbandingan-subkriteria.hasil', $dataView);
    }
}
