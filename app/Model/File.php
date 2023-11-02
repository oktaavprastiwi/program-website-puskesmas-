<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = ['path', 'filename'];

    public function sales()
    {
        return $this->hasMany('App\Model\Sale');
    }

    public function centroids()
    {
        return $this->hasMany('App\Model\Centroid');
    }
}
