<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    protected $table = 'transaksi_detail';
    public function produk()
    {
        return $this->belongsTo('App\Produk', 'produk_id');
    }
}
