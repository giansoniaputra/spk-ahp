<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Rangking;
use App\Models\Alternatif;
use App\Models\PvKriteria;
use App\Models\Perhitungan;
use App\Models\SubKriteria;
use Illuminate\Http\Request;
use App\Models\PvSubKriteria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

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

    public function perhitungan_perangkingan()
    {
        $pvKriteria = PvKriteria::all();
        $alternatifs = Alternatif::all();
        $nama = [];
        $nilai = [];

        foreach ($alternatifs as $alternatif) {
            $pvSub = DB::table('pv_sub_kriteria as a')
                ->join('sub_kriteria as b', 'a.sub_kriteria_id', '=', 'b.id')
                ->join('alternatif as c', 'c.id', '=', 'b.sub_kriteria')
                ->select('a.*', 'b.sub_kriteria', 'c.alternatif')
                ->where('b.sub_kriteria', $alternatif->id)
                ->get();
            $tampung = [];
            foreach ($pvSub as $row) {
                $tampung[] = $row->nilai;
            }
            $tampung2 = [];
            for ($i = 0; $i < count($pvKriteria); $i++) {
                $tampung2[] = round(($pvKriteria[$i]->nilai * $tampung[$i]), 4);
            }
            $nama[] = $alternatif->id;
            $nilai[] =  array_sum($tampung2);
        }
        Rangking::truncate();
        for ($j = 0; $j < count($nama); $j++) {
            $data = [
                'alternatif_id' => $nama[$j],
                'nilai' => $nilai[$j],
            ];
            Rangking::create($data);
        }
        $data2 = [
            'ranking' => DB::table('rangking as a')
                ->join('alternatif as b', 'a.alternatif_id', '=', 'b.id')
                ->select('a.*', 'b.alternatif', 'b.keterangan')
                ->orderBy('nilai', 'desc')->get()
        ];
        $view = View::make('perhitungan.render_ranking', $data2)->render();

        return response()->json(['view' => $view]);
    }

    public function ranking()
    {
        $data2 = [
            'title' => '',
            'ranking' => DB::table('rangking as a')
                ->join('alternatif as b', 'a.alternatif_id', '=', 'b.id')
                ->select('a.*', 'b.alternatif', 'b.keterangan')
                ->orderBy('nilai', 'desc')->get()
        ];
        return view('perhitungan.ranking', $data2);
    }
}
