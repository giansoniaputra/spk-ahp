<?php

namespace App\Http\Controllers;

use App\Models\KriteriaSAW;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AlternatifSAW;
use App\Models\PerhitunganSAW;
use App\Models\SubKriteriaSAW;
use Illuminate\Support\Facades\DB;

class PerhitunganSAWController extends Controller
{

    public function index_saw()
    {
        $data = [
            'title' => 'Perhitungan SAW',
            'perhitungan' => DB::table('perhitungan_s_a_w_s as a')
                ->join('alternatif_s_a_w_s as b', 'a.alternatif_uuid', '=', 'b.uuid')
                ->select('a.*', 'b.alternatif', 'b.keterangan')
                ->orderBy('b.alternatif', 'asc'),
            'kriterias' => KriteriaSAW::orderBy('kode', 'asc')->get(),
            'alternatifs' => AlternatifSAW::orderBy('alternatif', 'asc')->get(),
            'sum_kriteria' => KriteriaSAW::count('id'),
        ];
        return view('saw.saw.index', $data);
    }

    public function create()
    {
        $cek = PerhitunganSAW::first();
        if (!$cek) {
            $kriterias = KriteriaSAW::orderBy('kode', 'asc')->get();
            $alternatifs = AlternatifSAW::orderBy('alternatif', 'asc')->get();
            foreach ($alternatifs as $alternatif) {
                foreach ($kriterias as $kriteria) {
                    $data = [
                        'uuid' => Str::orderedUuid(),
                        'alternatif_uuid' => $alternatif->uuid,
                        'kriteria_uuid' => $kriteria->uuid,
                        'bobot' => 0
                    ];
                    PerhitunganSAW::create($data);
                }
            }
            return response()->json(['success' => 'Perhitungan Baru Berhasil Ditambahkan! Silahkan Masukan Nilainya']);
        } else {
            $kriterias = KriteriaSAW::orderBy('kode', 'asc')->get();
            $alternatifs = AlternatifSAW::orderBy('alternatif', 'asc')->get();
            foreach ($alternatifs as $alternatif) {
                $query = PerhitunganSAW::where('alternatif_uuid', $alternatif->uuid)->first();
                if (!$query) {
                    foreach ($kriterias as $kriteria) {
                        $data = [
                            'uuid' => Str::orderedUuid(),
                            'alternatif_uuid' => $alternatif->uuid,
                            'kriteria_uuid' => $kriteria->uuid,
                            'bobot' => 0
                        ];
                        PerhitunganSAW::create($data);
                    }
                }
            }
            foreach ($kriterias as $kriteria) {
                $query = PerhitunganSAW::where('kriteria_uuid', $kriteria->uuid)->first();
                if (!$query) {
                    foreach ($alternatifs as $alternatif) {
                        $data = [
                            'uuid' => Str::orderedUuid(),
                            'alternatif_uuid' => $alternatif->uuid,
                            'kriteria_uuid' => $kriteria->uuid,
                            'bobot' => 0
                        ];
                        PerhitunganSAW::create($data);
                    }
                }
            }
            return response()->json(['success' => 'Perhitungan Baru Berhasil Ditambahkan! Silahkan Masukan Nilainya']);
        }
    }

    public function update(PerhitunganSAW $perhitungan, Request $request)
    {
        PerhitunganSAW::where('uuid', $perhitungan->uuid)->update(['bobot' => $request->bobot]);
        return response()->json(['success' => $request->bobot]);
    }

    public function normalisasi()
    {
        //Inisialisasi Normalisasi
        $data = [
            'title' => 'Normalisasi',
            'perhitungan' => DB::table('perhitungan_s_a_w_s as a')
                ->join('alternatif_s_a_w_s as b', 'a.alternatif_uuid', '=', 'b.uuid')
                ->select('a.*', 'b.alternatif', 'b.keterangan')
                ->orderBy('b.alternatif', 'asc'),
            'kriterias' => KriteriaSAW::orderBy('kode', 'asc')->get(),
            'alternatifs' => AlternatifSAW::orderBy('alternatif', 'asc')->get(),
            'sum_kriteria' => KriteriaSAW::count('id'),
        ];
        $elements = '';
        $array_bobot = [];
        foreach ($data['alternatifs'] as $alternatif) {
            $elements .= "<tr><td>A$alternatif->alternatif</td>
            <td>$alternatif->keterangan</td>";
            foreach ($data['kriterias'] as $kriteria) {
                $bobots = DB::table('perhitungan_s_a_w_s')
                    ->where('kriteria_uuid', $kriteria->uuid)
                    ->where('alternatif_uuid', $alternatif->uuid)
                    ->get();
                foreach ($bobots as $bobot) {
                    $max = SubKriteriaSAW::where('kriteria_uuid', $kriteria->uuid)->orderBy('bobot', 'desc')->first();
                    $bobot_kriteria = round($bobot->bobot / $max->bobot, 3);
                    $elements .= "<td class=\"text-center\" id=\"nilai-bobot\">
                                        <p class=\"p-bobot\">" . $bobot_kriteria . "</p>
                                        <form action=\"javascript:;\" id=\"form-update-bobot\">
                                            <input type=\"number\" class=\"d-none input-bobot\" data-uuid=" . $bobot_kriteria . " value=\"" . $bobot_kriteria . "\" style=\"width:6vh\">
                                        </form>
                                    </td>";
                    $array_bobot[] = $bobot_kriteria;
                }
            }
            $elements .= "</tr>";
        }
        $data['elements'] = $elements;
        //MENGHITUNG RANKING-----------------------------------------------
        $bobot_kriteria = array_chunk($array_bobot, $data['sum_kriteria']);

        //Mengambil Bobot Kriteria
        $bobot_pembagi = 0;
        foreach ($data['kriterias'] as $kriteria) {
            $bobot_pembagi += $kriteria->bobot;
        }
        $bobot = [];
        foreach ($data['kriterias'] as $kriteria) {
            $bobot[] = $kriteria->bobot / $bobot_pembagi;
        }
        //Meng kalikan bobot dengan normalisasi
        $hasil_kali = [];
        for ($i = 0; $i < count($bobot_kriteria); $i++) {
            for ($j = 0; $j < count($bobot); $j++) {
                $hasil_kali[] = floatval(number_format($bobot_kriteria[$i][$j] * $bobot[$j], 3));
            }
        }

        //hasil perkalian di pecah menjadi array muti dimensi
        $pecah_hasil = array_chunk($hasil_kali, $data['sum_kriteria']);

        // Perkalian Semua Array
        $ranking = [];
        for ($u = 0; $u < count($pecah_hasil); $u++) {
            $ranking[] = round(array_sum($pecah_hasil[$u]), 3);
        }

        //Merangking
        $nama = AlternatifSAW::orderBy('alternatif', 'asc')->get();
        $rangking_assoc = [];
        foreach ($ranking as $index => $nilai) {
            $rangking_assoc[] = [$nama[$index]->keterangan, $nilai];
        }

        $names = array_column($rangking_assoc, 0);
        $scores = array_column($rangking_assoc, 1);

        // Menggunakan array_multisort untuk mengurutkan scores secara menurun
        array_multisort($scores, SORT_DESC, $names);

        // Menggabungkan kembali array setelah diurutkan
        $final_ranking = array_map(function ($name, $score) {
            return [$name, $score];
        }, $names, $scores);

        $data['ranking'] = $final_ranking;

        return response()->json(['data' => $data]);
    }
    public function normalisasi_waspas()
    {
        //Inisialisasi Normalisasi
        $data = [
            'title' => 'Normalisasi',
            'perhitungan' => DB::table('perhitungan_s_a_w_s as a')
                ->join('alternatif_s_a_w_s as b', 'a.alternatif_uuid', '=', 'b.uuid')
                ->select('a.*', 'b.alternatif', 'b.keterangan')
                ->orderBy('b.alternatif', 'asc'),
            'kriterias' => KriteriaSAW::orderBy('kode', 'asc')->get(),
            'alternatifs' => AlternatifSAW::orderBy('alternatif', 'asc')->get(),
            'sum_kriteria' => KriteriaSAW::count('id'),
        ];
        $elements = '';
        $array_bobot = [];
        foreach ($data['alternatifs'] as $alternatif) {
            $elements .= "<tr><td>A$alternatif->alternatif</td>
            <td>$alternatif->keterangan</td>";
            foreach ($data['kriterias'] as $kriteria) {
                $bobots = DB::table('perhitungan_s_a_w_s')
                    ->where('kriteria_uuid', $kriteria->uuid)
                    ->where('alternatif_uuid', $alternatif->uuid)
                    ->get();
                foreach ($bobots as $bobot) {
                    if ($kriteria->atribut == 'BENEFIT') {
                        $max = SubKriteriaSAW::where('kriteria_uuid', $kriteria->uuid)->orderBy('bobot', 'desc')->first();
                        $bobot_kriteria = round($bobot->bobot / $max->bobot, 3);
                    } else {
                        $min = SubKriteriaSAW::where('kriteria_uuid', $kriteria->uuid)->orderBy('bobot', 'asc')->first();
                        $bobot_kriteria = round($min->bobot / $bobot->bobot, 3);
                    }
                    $elements .= "<td class=\"text-center\" id=\"nilai-bobot\">
                                        <p class=\"p-bobot\">" . $bobot_kriteria . "</p>
                                        <form action=\"javascript:;\" id=\"form-update-bobot\">
                                            <input type=\"number\" class=\"d-none input-bobot\" data-uuid=" . $bobot_kriteria . " value=\"" . $bobot_kriteria . "\" style=\"width:6vh\">
                                        </form>
                                    </td>";
                    $array_bobot[] = $bobot_kriteria;
                }
            }
            $elements .= "</tr>";
        }
        $data['elements'] = $elements;
        //MENGHITUNG RANKING-----------------------------------------------
        $bobot_kriteria = array_chunk($array_bobot, $data['sum_kriteria']);

        //Mengambil Bobot Kriteria
        $bobot = [];
        foreach ($data['kriterias'] as $kriteria) {
            $bobot[] = $kriteria->bobot / 100;
        }
        //Meng kalikan bobot dengan normalisasi
        $hasil_kali = [];
        $hasil_pangkat = [];
        for ($i = 0; $i < count($bobot_kriteria); $i++) {
            for ($j = 0; $j < count($bobot); $j++) {
                $hasil_kali[] = floatval(number_format($bobot_kriteria[$i][$j] * $bobot[$j], 3));
                $hasil_pangkat[] = floatval(number_format(pow($bobot_kriteria[$i][$j], $bobot[$j]), 3));
            }
        }




        //hasil perkalian di pecah menjadi array muti dimensi
        $pecah_kali = array_chunk($hasil_kali, $data['sum_kriteria']);
        $pecah_pangkat = array_chunk($hasil_pangkat, $data['sum_kriteria']);


        // Perkalian Semua Array
        $sigma1 = [];
        for ($u = 0; $u < count($pecah_kali); $u++) {
            $sigma1[] = round(0.5 * round(array_sum($pecah_kali[$u]), 3), 3);
        }
        $sigma2 = [];
        for ($u = 0; $u < count($pecah_pangkat); $u++) {
            $sigma2[] = round(0.5 * round(array_reduce($pecah_pangkat[$u], function ($carry, $item) {
                return $carry * $item;
            }, 1), 3), 3);
        }

        $ranking = array_map(function ($x, $y) {
            return round($x + $y, 3);
        }, $sigma1, $sigma2);


        //Merangking
        $nama = AlternatifSAW::orderBy('alternatif', 'asc')->get();
        $rangking_assoc = [];
        foreach ($ranking as $index => $nilai) {
            $rangking_assoc[] = [$nama[$index]->keterangan, $nilai];
        }

        $names = array_column($rangking_assoc, 0);
        $scores = array_column($rangking_assoc, 1);

        // Menggunakan array_multisort untuk mengurutkan scores secara menurun
        array_multisort($scores, SORT_DESC, $names);

        // Menggabungkan kembali array setelah diurutkan
        $final_ranking = array_map(function ($name, $score) {
            return [$name, $score];
        }, $names, $scores);

        $data['ranking'] = $final_ranking;

        return response()->json(['data' => $data]);
    }
}
