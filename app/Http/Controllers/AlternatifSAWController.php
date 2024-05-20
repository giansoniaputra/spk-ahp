<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AlternatifSAW;
use App\Models\PerhitunganSAW;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class AlternatifSAWController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Alternatif'
        ];
        return view('saw.alternatif.index', $data);
    }

    public function store(Request $request)
    {
        $rules = [
            'alternatif' => 'required|unique:alternatif_s_a_w_s',
            'keterangan' => 'required',
        ];
        $pesan = [
            'alternatif.required' => 'Alternatif tidak boleh kosong!',
            'alternatif.unique' => 'Alternatif sudah ada!',
            'keterangan.required' => 'Ketarangan tidak boleh kosong!',
        ];
        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        } else {
            $data = [
                'uuid' => Str::orderedUuid(),
                'alternatif' => $request->alternatif,
                'keterangan' => $request->keterangan,
            ];
            AlternatifSAW::create($data);
            return response()->json(['success' => 'Alternatif Berhasi Di Buat!']);
        }
    }

    public function edit(AlternatifSAW $alternatif)
    {
        return response()->json(['data' => $alternatif]);
    }

    public function update(AlternatifSAW $alternatif, Request $request)
    {
        $rules = [
            'alternatif' => 'required',
            'keterangan' => 'required',
        ];
        $pesan = [
            'alternatif.required' => 'Alternatif tidak boleh kosong!',
            'keterangan.required' => 'Ketarangan tidak boleh kosong!',
        ];
        $cek = AlternatifSAW::where('uuid', $request->uuid)->first();
        if ($cek->alternatif == $request->alternatif) {
            $rules['alternatif'] = 'required';
            $pesan['alternatif.required'] = 'alternatif tidak boleh kosong';
        } else {
            $rules['alternatif'] = 'required|unique:alternatif_s_a_w_s';
            $pesan['alternatif.unique'] = 'alternatif sudah ada';
            $pesan['alternatif.required'] = 'alternatif tidak boleh kosong';
        }
        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        } else {
            $data = [
                'alternatif' => $request->alternatif,
                'keterangan' => $request->keterangan,
            ];
            AlternatifSAW::where('uuid', $alternatif->uuid)->update($data);
            return response()->json(['success' => 'Alternatif Berhasi Di Ubah!']);
        }
    }

    public function destroy(AlternatifSAW $alternatif)
    {
        AlternatifSAW::destroy($alternatif->id);
        PerhitunganSAW::where('alternatif_uuid', $alternatif->uuid)->delete();
        return response()->json(['success' => 'Alternatif Berhasil Di Hapus!']);
    }

    public function dataTablesAlternatif()
    {
        $query = AlternatifSAW::all();
        foreach ($query as $row) {
            $row->alternatif = "A" . $row->alternatif;
        }
        return DataTables::of($query)->addColumn('action', function ($row) {
            $actionBtn =
                '
                <button class="btn btn-rounded btn-sm btn-warning text-dark edit-button" title="Edit Data" data-uuid="' . $row->uuid . '"><i class="fas fa-edit"></i></button>
                <button class="btn btn-rounded btn-sm btn-danger text-white delete-button" title="Hapus Data" data-uuid="' . $row->uuid . '" data-token="' . csrf_token() . '"><i class="fas fa-trash-alt"></i></button>';
            return $actionBtn;
        })->make(true);
    }
}
