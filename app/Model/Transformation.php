<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Transformation extends Model
{
    protected $fillable = [
        'file_id',
        'umur',
        'jenis_kelamin',
        'nama_penyakit',
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
