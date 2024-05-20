<form class="ui form" action="/perbandingan-subkriterias" method="post">
    @csrf
    <table class="ui celled selectable collapsing table">
        <thead>
            <tr>
                <th colspan="2">Pilih yang Lebih Penting</th>
                <th>Nilai Perbandingan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $urut = 0;
            @endphp
            @for ($x = 0; $x <= ($n - 2); $x++)
                @for ($y = ($x + 1); $y <= ($n - 1); $y++)
                @php
                    $urut++
                @endphp
                <tr>
                    <td>
                        <div class="field">
                            <div class="ui radio checkbox">
                                <input name="pilih<?php echo $urut ?>" value="1"  class="hidden" type="radio"
                                {{ (getCheckedSubkriteria($x, $y, $kriteria_id) == 1) ? 'checked' : ''}}
                                >
                                <label><?php echo $pilihan[$x]; ?></label>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <div class="ui radio checkbox">
                                <input name="pilih<?php echo $urut ?>" value="2" class="hidden" type="radio" {{ (getCheckedSubkriteria($x, $y, $kriteria_id) == 2) ? 'checked' : ''}}>
                                <label><?php echo $pilihan[$y]; ?></label>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="field">

                            
                            
                            <input type="text" name="bobot{{ $urut }}" value="{{ getNilaiPerbandinganSubKriteria($x, $y, $kriteria_id)}}" required>
                        </div>
                    </td>
                </tr>
                @endfor
            @endfor
        </tbody>
    </table>
    <input type="text" name="jenis" value="{{ $jenis }}" hidden>
    <input type="hidden" name="kriteria_id" id="kriteria_id" value="{{ $kriteria_id }}">
    <br><br><input class="btn btn-primary" type="submit" name="submit" value="SUBMIT">
</form>