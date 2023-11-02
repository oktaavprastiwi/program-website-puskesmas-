<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    protected $table = 'diagnosis';
    protected $fillable = [
        'file_id',
        'tanggal',
        'no_index',
        'kode_penyakit',
        'diagnosa',
        'alamat',
        'jenis_kelamin',
        'umur',
        'unit',
    ];

    public function file()
    {
        return $this->belongsTo('App\Model\File');
    }
}
