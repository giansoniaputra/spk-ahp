<?php

namespace App\Http\Controllers;

use App\Models\SubKriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;


class SubKriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function dataTables(Request $request)
    {
        $query = SubKriteria::where('kriteria_id', $request->kriteria_id)->get();
        return DataTables::of($query)->addColumn('action', function ($row) {
            $actionBtn =
                '
                <button class="btn btn-rounded btn-sm btn-warning text-dark edit-button" title="Edit Data" data-unique="' . $row->unique . '"><i class="fas fa-edit"></i></button>
                <button class="btn btn-rounded btn-sm btn-danger text-white delete-button" title="Hapus Data" data-unique="' . $row->unique . '" data-token="' . csrf_token() . '"><i class="fas fa-trash-alt"></i></button>';
            return $actionBtn;
        })->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'sub_kriteria' => 'required',
            'nilai' => 'required',
            'bobot' => 'required',
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $data['unique'] = Str::orderedUuid();
        $data['kriteria_id'] = $request->kriteria_id;

        SubKriteria::create($data);
        return response()->json(['success' => "Sub Kategori berhasil di tambahkan"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Subkriteria $subkriteria)
    {
        return response()->json(['data' => $subkriteria]);
    }

    public function update(Request $request, Subkriteria $subkriteria)
    {
        $rules = [
            'sub_kriteria' => 'required',
            'nilai' => 'required',
            'bobot' => 'required',
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $subkriteria->fill($data);
        $subkriteria->save();
        return response()->json(['success' => "Sub Kategori berhasil di tanbahkan"]);
    }

    public function destroy(SubKriteria $subkriteria)
    {
        SubKriteria::destroy($subkriteria->id);
        return response()->json(['success' => 'Data Sub Kriteria Behasil Dihapus']);
    }
}
