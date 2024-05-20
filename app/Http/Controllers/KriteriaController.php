<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class KriteriaController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Kriteria'
        ];
        return view('kriteria.index', $data);
    }

    public function dataTables()
    {
        $query = Kriteria::all();
        return DataTables::of($query)->addColumn('action', function ($row) {
            $actionBtn =
                '
                <button class="btn btn-rounded btn-sm btn-success text-white sub-button" title="Sub Kriteria" data-kriteria_id="' . $row->id . '" data-judul="' . $row->kriteria . '"><i class="fas fa-plus"></i></button>
                <button class="btn btn-rounded btn-sm btn-warning text-dark edit-button" title="Edit Data" data-unique="' . $row->unique . '"><i class="fas fa-edit"></i></button>
                <button class="btn btn-rounded btn-sm btn-danger text-white delete-button" title="Hapus Data" data-unique="' . $row->unique . '" data-token="' . csrf_token() . '"><i class="fas fa-trash-alt"></i></button>';
            return $actionBtn;
        })->make(true);
    }

    public function store(Request $request)
    {
        $rules = [
            'kriteria' => 'required',
        ];
        $data = $request->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $data['unique'] = Str::orderedUuid();

        Kriteria::create($data);
        return response()->json(['success' => 'Kriteria Berhasil Disimpan']);
    }

    public function edit(Kriteria $kriteria)
    {
        return response()->json(['data' => $kriteria]);
    }

    public function update(Request $request, Kriteria $kriteria)
    {
        $rules = [
            'kriteria' => 'required',
        ];
        $data = $request->all();
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }


        $kriteria->fill($data);
        $kriteria->save();
        return response()->json(['success' => 'Data Kriteria Berhasil Diedit']);
    }

    public function destroy(Kriteria $kriteria)
    {
        Kriteria::destroy($kriteria->id);
        return response()->json(['success' => 'Data Kriteria Behasil Dihapus']);
    }
}
