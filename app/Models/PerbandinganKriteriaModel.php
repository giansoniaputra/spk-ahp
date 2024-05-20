<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerbandinganKriteriaModel extends Model
{
    use HasFactory;
    protected $table = 'perbandingan_kriteria';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

    protected $guarded = ['id'];

    public function getRouteKeyName()
    {
        return 'unique';
    }
}
