<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = "siswa";
    public function transaksi()
    {
        return $this->hasMany('App\Transaksi');
    }
}
