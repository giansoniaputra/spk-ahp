<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    use HasFactory;
    protected $table = 'kriteria';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

    protected $guarded = ['id'];

    public function getRouteKeyName()
    {
        return 'unique';
    }

    public function sub_kriteria()
    {
        return $this->hasMany('App\Models\SubKriteria');
    }
}
