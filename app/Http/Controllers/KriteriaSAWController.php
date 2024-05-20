<?php

namespace App\Http\Controllers;

use App\Models\KriteriaSAW;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PerhitunganSAW;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class KriteriaSAWController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'title' => 'Kriteria'
        ];
        return view('saw.kriteria.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'kode' => 'required|unique:kriteria_s_a_w_s',
            'kriteria' => 'required',
            'atribut' => 'required',
            'bobot' => 'required',
        ];
        $pesan = [
            'kode.required' => "Kode tidak boleh kosong",
            'kode.unique' => "Kode sudah ada",
            'kriteria.required' => "Kriteria tidak boleh kosong",
            'atribut.required' => "Atribut tidak boleh kosong",
            'bobot.required' => "Atribut tidak boleh kosong",
        ];
        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        } else {
            $data = [
                'uuid' => Str::orderedUuid(),
                'kode' => $request->kode,
                'kriteria' => $request->kriteria,
                'atribut' => $request->atribut,
                'bobot' => $request->bobot,
            ];
            KriteriaSAW::create($data);
            return response()->json(['success' => 'Kriteria Berhasil Disimpan']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(KriteriaSAW $kriteria)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KriteriaSAW $kriteria, Request $request)
    {
        $data = KriteriaSAW::where('uuid', $request->uuid)->first();
        return response()->json(['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KriteriaSAW $kriteria)
    {
        $rules = [
            'kriteria' => 'required',
            'atribut' => 'required',
            'bobot' => 'required',
        ];
        $pesan = [
            'kriteria.required' => "Kriteria tidak boleh kosong",
            'atribut.required' => "Atribut tidak boleh kosong",
            'bobot.required' => "Atribut tidak boleh kosong",
        ];
        $cek = KriteriaSAW::where('uuid', $request->uuid)->first();
        if ($cek->kode == $request->kode) {
            $rules['kode'] = 'required';
            $pesan['kode.required'] = 'Kode tidak boleh kosong';
        } else {
            $rules['kode'] = 'required|unique:kriteria_s_a_w_s';
            $pesan['kode.unique'] = 'Kode sudah ada';
            $pesan['kode.required'] = 'Kode tidak boleh kosong';
        }

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        } else {
            $data = [
                'kode' => strtoupper($request->kode),
                'kriteria' => $request->kriteria,
                'atribut' => $request->atribut,
                'bobot' => $request->bobot,
            ];
            KriteriaSAW::where('uuid', $request->uuid)->update($data);
            return response()->json(['success' => 'Kriteria Berhasil Disimpan']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KriteriaSAW $kriteria, Request $request)
    {
        KriteriaSAW::where('uuid', $request->uuid)->delete();
        PerhitunganSAW::where('kriteria_uuid', $request->uuid)->delete();
        return response()->json(['success' => 'Data Kriteria Berhasil Dihapus']);
    }

    public function dataTablesKriteria(Request $request)
    {
        $query = KriteriaSAW::all();
        foreach ($query as $row) {
            $row->bobot = $row->bobot;
            $row->kode = 'C' . $row->kode;
        }
        return DataTables::of($query)->addColumn('action', function ($row) {
            $actionBtn =
                '
                <button class="btn btn-rounded btn-sm btn-success text-white sub-button" title="Sub Kriteria" data-uuid="' . $row->uuid . '" data-judul="' . $row->kriteria . '"><i class="fas fa-plus"></i></button>
                <button class="btn btn-rounded btn-sm btn-warning text-dark edit-button" title="Edit Data" data-uuid="' . $row->uuid . '"><i class="fas fa-edit"></i></button>
                <button class="btn btn-rounded btn-sm btn-danger text-white delete-button" title="Hapus Data" data-uuid="' . $row->uuid . '" data-token="' . csrf_token() . '"><i class="fas fa-trash-alt"></i></button>';
            return $actionBtn;
        })->make(true);
    }
}
