<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perhitungan extends Model
{
    use HasFactory;
    protected $table = 'perhitungan';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

    protected $guarded = ['id'];

    public function alternatif()
    {
        return $this->belongsTo('App\Models\Alternatif');
    }

    public function kriteria()
    {
        return $this->belongsTo('App\Models\Kriteria');
    }

    public function sub_kriteria()
    {
        return $this->belongsTo('App\Models\SubKriteria');
    }
}
