@extends('layouts.main')
@section('container')
<div class="card">
	<div class="card-header">
		<h3 class="ui header">Matriks Perbandingan Berpasangan</h3>
	</div>
	<div class="card-body">
		<section class="content">
			
			<table class="ui collapsing celled blue table table-sm">
				<thead>
					<tr>
						<th>Sub Kriteria</th>
						@for ($i = 0; $i <= ($n - 1); $i++)
							<th>{{ getSubKriteriaNama($i, $kriteria_id)->sub_kriteria }}</th>
						@endfor
					</tr>
				</thead>
				<tbody>
					@for ($x = 0; $x <= ($n - 1); $x++)
						<tr>
							<td>
								{{ getSubKriteriaNama($x, $kriteria_id)->sub_kriteria }}
							</td>
							@for ($y = 0; $y <= ($n - 1); $y++)
								<td>
									{{ round($matrik[$x][$y], 5) }}
								</td>
							@endfor
						</tr>
					@endfor
				</tbody>
				<tfoot>
					<tr>
						<th>Jumlah</th>
						@for ($i = 0; $i <= ($n - 1); $i++)
							<th> {{ round($jmlmpb[$i], 5) }}</th>
						@endfor
					</tr>
				</tfoot>
			</table>


			<br>

			<h3 class="ui header">Matriks Nilai Sub Kriteria</h3>
			<table class="ui celled red table">
				<thead>
					<tr>
						<th>Sub Kriteria</th>
						@for ($i = 0; $i <= ($n - 1); $i++)
							<th>{{ getSubKriteriaNama($i, $kriteria_id)->sub_kriteria }}</th>
						@endfor
						<th>Jumlah</th>
						<th>Priority Vector</th>
					</tr>
				</thead>
				<tbody>
					@for ($x = 0; $x <= ($n - 1); $x++)
						<tr>
							<td>{{ getSubKriteriaNama($x, $kriteria_id)->sub_kriteria }}</td>
							@for ($y = 0; $y <= ($n - 1); $y++)
							<td>{{ round($matrikb[$x][$y], 5) }}</td>
							@endfor
							<td>{{ round($jmlmnk[$x], 5) }}</td>
							<td>{{ round($pv[$x], 5) }}</td>
						</tr>
					@endfor
				</tbody>
				<tfoot>
					<tr>
						<th colspan="{{ $n + 2 }}">Principe Eigen Vector (λ maks)</th>
						<th>{{ round($eigenvektor, 5) }}</th>
					</tr>
					<tr>
						<th colspan="{{ $n + 2 }}">Consistency Index</th>
						<th>{{ round($consIndex, 5) }}</th>
					</tr>
					<tr>
						<th colspan="{{ $n + 2 }}">Consistency Ratio</th>
						<th>{{ round(($consRatio), 2) }}</th>
					</tr>
				</tfoot>
			</table>
			@if ($consRatio > 0.1)
			<div class="ui icon red message">
				<i class="close icon"></i>
				<i class="warning circle icon"></i>
				<div  class="alert alert-danger" role="alert">
					<div class="header">
						Nilai Consistency Ratio melebihi 10% !!!
					</div>
					<p>Mohon input kembali tabel perbandingan...</p>
				</div>
			</div>

			<br>

			<a href='javascript:history.back()'>
				<button class="btn btn-secondary">
					Kembali
				</button>
			</a>
			@else
                @if ($jenis == getJumlahKriteria())
                <form action="/perhitungans">
                    <button class="btn btn-success" style="float: right;">
                        <i class="right arrow icon"></i>
                        Lanjut Perhitungan
                    </button>
                </form>
                @else
                <br>
                <a href="/perbandingan-subkriterias?data={{ $jenis + 1 }}">
                    <button class="btn btn-primary" style="float: right;">
                        <i class="right arrow icon"></i>
                        Lanjut
                    </button>
                </a>
                @endif
			
			@endif
		</section>
	</div>
</div>

	
	
@endsection