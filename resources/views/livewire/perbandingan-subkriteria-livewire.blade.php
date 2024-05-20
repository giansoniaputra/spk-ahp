@extends('layouts.main')
@section('container')
<div class="row mb-2">
    <div class="col-sm-6">
        <div class="card">
            <div class="card-header"><h6>Kriteria &rarr; {{ getKriteriaNama($jenis-1)->kriteria }}</h6></div>
            <div class="card-body">
                {{ showTabelPerbandinganSubkriteria($jenis,getKriteriaID($jenis-1)->id) }}
            </div>
        </div>
	    
    </div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
</div>
@endsection
