@extends('layouts.main')
@section('container')
<div class="row mb-2">
    <div class="col-sm-6">
        <div class="card">
            <form action="/perbandingan-kriterias" method="POST">
                @csrf
            <div class="card-header">

            </div>
            <div class="card-body">
                <table class="table  table-bordered table-hover">
                    <thead>
                        <tr>
                            <th colspan="2">Pilih yang Lebih Penting</th>
                            <th>Nilai Perbandingan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $urut = 1;
                            $n = count($kriteria);
                        @endphp
                        @for ($x = 0; $x <= ($n - 2); $x++)
                            @for ($y = ($x + 1); $y <= ($n - 1); $y++)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="pilih{{ $urut }}" id="pilih{{ $urut }}" value="1" 
                                        {{ (getChecked($x, $y) == 1) ? 'checked' : ''}}>
                                        <label class="form-check-label" for="pilih{{ $urut }}">
                                        {{ $kriteria[$x]['kriteria'] }}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="pilih{{ $urut }}" id="pilih{{ $urut }}" value="2"
                                        {{ (getChecked($x, $y) == 2) ? 'checked' : ''}}>
                                        <label class="form-check-label" for="pilih{{ $urut }}">
                                        {{ $kriteria[$y]['kriteria'] }}
                                        </label>
                                    </div>
                                </td>
                                @php
                                    $nilai = (!empty($perbandingan_kriteria)) ? getNilaiPerbandinganKriteria($x, $y) : '';
                                @endphp
                                <td>
                                    <input type="number" name="bobot{{ $urut++ }}" class="form-control" value="{{ $nilai }}" required>
                                </td>
                            </tr>
                            @endfor
                        @endfor
                        
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary" type="submit">Submit</button>
            </div>
            </form>
        </div>
    </div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
</div>
@endsection

