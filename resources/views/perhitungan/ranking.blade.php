@extends('layouts.main')
@section('container')
<div class="row mb-2">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h2>Ranking</h2>
            </div>
            <div class="card-body">
                <table id="table-kriteria" class="table table-bordered table-hover dataTable dtr-inline">
                    <thead>
                        <th width="5%">No</th>
                        <th>Alternatif</th>
                        <th>Ranking</th>
                    </thead>
                    <tbody>
                        @foreach ($ranking as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->alternatif }}</td>
                            <td>{{ $row->nilai }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
