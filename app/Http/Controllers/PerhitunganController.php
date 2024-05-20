<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Perhitungan;
use App\Models\SubKriteria;
use Illuminate\Http\Request;

class PerhitunganController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Perhitungan AHP',
            'kriteria' => Kriteria::get(),
            'perhitungan' => Perhitungan::with('sub_kriteria')->with('kriteria')->with('alternatif')->get(),
            'alternatif' => Alternatif::get(),
        ];
        // return response()->json($data);
        return view('perhitungan.index', $data);
    }

    public function create(Request $request)
    {
        $cek = Perhitungan::get();
        $kriteria = Kriteria::orderBy('id')->get();
        $alternatif = Alternatif::orderBy('id')->get();
        if ($cek->count() == 0) {

            foreach ($kriteria as $itemKriteria) {
                foreach ($alternatif as $itemAlternatif) {
                    $data = [
                        'alternatif_id' => $itemAlternatif['id'],
                        'kriteria_id' => $itemKriteria['id'],
                        'sub_kriteria_id' => null
                    ];
                    Perhitungan::create($data);
                }
            };
        } else {
            foreach ($kriteria as $itemKriteria) {
                foreach ($alternatif as $itemAlternatif) {
                    $perhitungan = Perhitungan::where('alternatif_id', $itemAlternatif['id'])->where('kriteria_id', $itemKriteria['id'])->first();

                    $data['sub_kriteria_id'] = null;

                    $perhitungan->fill($data);
                    $perhitungan->save();
                }
            }
        }
        return response()->json(['success' => 'Perhitungan Baru Berhasil Ditambahkan! Silahkan Masukan Nilainya']);
    }

    public function update($id, Request $request)
    {
        $perhitungan = Perhitungan::with('sub_kriteria')->where('id', $id)->first();
        $data['sub_kriteria_id'] = $request->bobot;

        $sub_kriteria = SubKriteria::where('id', $request->bobot)->first();
        $perhitungan->fill($data);
        $perhitungan->save();
        return response()->json(['success' => $sub_kriteria->nilai, 'sub_kriteria_id' => $sub_kriteria->id]);
    }

    public function normalisasi()
    {
        //Inisialisasi Normalisasi
        $data = [
            'title' => 'Normalisasi',
            'kriteria' => Kriteria::get(),
            'perhitungan' => Perhitungan::with('sub_kriteria')->with('kriteria')->with('alternatif')->get(),
            'alternatif' => Alternatif::get(),
            'sum_kriteria' => getJumlahKriteria()
        ];
        $elements = '';
        $array_bobot = [];
        $hasil = [];
        foreach ($data['alternatif'] as $itemAlternatif) {
            $elements .= "<tr><td>$itemAlternatif->alternatif</td>";
            $jumlahNilai = 0.0;
            foreach ($data['kriteria'] as $itemKriteria) {
                $elements   .= '<td class="text-center" id="nilai-bobot">' . getNilaiPvPerhitungan($itemAlternatif['id'], $itemKriteria['id']) . '</td>';
                $array_bobot[] =  getNilaiPvPerhitungan($itemAlternatif['id'], $itemKriteria['id']);

                $jumlahNilai += getNilaiPvPerhitungan($itemAlternatif['id'], $itemKriteria['id']) * getNilaiPvKriteria($itemKriteria['id']);
            }
            $hasil[] = number_format($jumlahNilai, 4);
            $elements .= "</tr>";
        }
        $data['elements'] = $elements;
        $data['hasil'] = $hasil;
        //MENGHITUNG RANKING-----------------------------------------------
        // Mengambil data perhitungan pv subkriteria dikalikan dengan kriteria

        // $rangking = '';
        // foreach ($data['alternatif'] as $itemAlternatif) {
        //     $rangking .= "<tr><td>$itemAlternatif->alternatif</td><td>";
        //     foreach ($data['kriteria'] as $itemKriteria) {
        //         getNilaiPvPerhitungan($itemAlternatif['id'], $itemKriteria['id']);
        //         $array_bobot[] =  getNilaiPvPerhitungan($itemAlternatif['id'], $itemKriteria['id']);
        //     }
        // }



        // $bobot_kriteria = array_chunk($array_bobot, getJumlahKriteria());

        // //Mengkalikan bobot dengan normalisasi
        // $hasil_kali = [];
        // for ($i = 0; $i < count($bobot_kriteria); $i++) {
        //     for ($j = 0; $j < count($bobot); $j++) {
        //         $hasil_kali[] = floatval(number_format($bobot_kriteria[$i][$j] * $bobot[$j], 3));
        //     }
        // }

        // //Mengambil Bobot Kriteria
        // $bobot = [];
        // foreach ($data['kriteria'] as $kriteria) {
        //     $bobot[] = $kriteria->bobot / 100;
        // }
        // //Meng kalikan bobot dengan normalisasi
        // $hasil_kali = [];
        // for ($i = 0; $i < count($bobot_kriteria); $i++) {
        //     for ($j = 0; $j < count($bobot); $j++) {
        //         $hasil_kali[] = floatval(number_format($bobot_kriteria[$i][$j] * $bobot[$j], 3));
        //     }
        // }

        // //hasil perkalian di pecah menjadi array muti dimensi
        // $pecah_hasil = array_chunk($hasil_kali, $data['sum_kriteria']);

        // // Perkalian Semua Array
        // $ranking = [];
        // for ($u = 0; $u < count($pecah_hasil); $u++) {
        //     $ranking[] = round(array_sum($pecah_hasil[$u]), 3);
        // }

        // //Merangking
        // $nama = Alternatif::orderBy('alternatif', 'asc')->get();
        $nama = $data['alternatif'];
        $rangking_assoc = [];
        foreach ($hasil as $index => $nilai) {
            $rangking_assoc[] = [$nama[$index]->alternatif, $nilai, $nama[$index]->keterangan];
        }

        $names = array_column($rangking_assoc, 0);
        $scores = array_column($rangking_assoc, 1);
        $keterangan = array_column($rangking_assoc, 2);

        // // Menggunakan array_multisort untuk mengurutkan scores secara menurun
        array_multisort($scores, SORT_DESC, $names, $keterangan);

        // // Menggabungkan kembali array setelah diurutkan
        $final_ranking = array_map(function ($name, $score, $keterangan) {
            return [$name, $score, $keterangan];
        }, $names, $scores, $keterangan);

        $data['rangkin_assoc'] = $rangking_assoc;

        $data['ranking'] = $final_ranking;

        // SAW-----------------------------------------------------------------________________________________

        return response()->json(['data' => $data, 'rangkin_assoc' => $rangking_assoc]);
    }
}
