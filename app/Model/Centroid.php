<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Centroid extends Model
{
    protected $fillable = [
        'file_id',
        'transform_umur',
        'transform_jenis_kelamin',
        'transform_diagnosa',
        'jumlah_penderita'
    ];

    public function file()
    {
        return $this->belongsTo('App\Model\File');
    }
}
