<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\DataTables;
use App\Models\Alternatif;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AlternatifController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Alternatif'
        ];
        return view('alternatif.index', $data);
    }

    public function dataTables()
    {
        $query = Alternatif::all();
        return DataTables::of($query)->addColumn('action', function ($row) {
            $actionBtn =
                '
                <button class="btn btn-rounded btn-sm btn-warning text-dark edit-button" title="Edit Data" data-unique="' . $row->unique . '"><i class="fas fa-edit"></i></button>
                <button class="btn btn-rounded btn-sm btn-danger text-white delete-button" title="Hapus Data" data-unique="' . $row->unique . '" data-token="' . csrf_token() . '"><i class="fas fa-trash-alt"></i></button>';
            return $actionBtn;
        })->make(true);
    }

    public function store(Request $request)
    {
        $rules = [
            'alternatif' => 'required|unique:alternatif'
        ];

        $data = $request->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $data['unique'] = Str::orderedUuid();
        Alternatif::create($data);
        return response()->json(['success' => 'Alternatif Berhasi Di Buat!']);
    }

    public function edit(Alternatif $alternatif)
    {
        return response()->json(['data' => $alternatif]);
    }

    public function update(Request $request, Alternatif $alternatif)
    {
        $rules = [
            'alternatif' => 'required|unique:alternatif'
        ];
        $data = $request->all();
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $alternatif->fill($data);
        $alternatif->save();
        return response()->json(['success' => 'Data SubKriteria Berhasil Diedit']);
    }

    public function destroy(Alternatif $alternatif)
    {
        Alternatif::destroy($alternatif->id);
        return response()->json(['success' => 'Data Kriteria Behasil Dihapus']);
    }
}
