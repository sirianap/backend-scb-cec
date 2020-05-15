<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{

    protected $table = "transaksi"; 
    public function detail()
    {
        return $this->hasMany('App\TransaksiDetail', 'transaksi_id');
    }
    public function siswa()
    {
        return $this->belongsTo('App\Siswa');
    }
    public function createdBy()
    {
        return $this->belongsTo('App\User','created_by');
    }
}
