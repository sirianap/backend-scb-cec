<?php

namespace App\Http\Controllers;

use App\Transaksi;
use App\Siswa;
use App\Produk;
use App\User;
use App\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $from = date($request->from);
        $to = date($request->to);
        if(!$from && !$to){
            $transaksi = Transaksi::where('mutasi_type','out')->where('saldo_type','card')->orderBy('created_at', 'desc')->with(['siswa','createdBy'])->get();
            return response()->json($transaksi, 200);
        }
        if(!$to){
            $transaksi = Transaksi::where('mutasi_type','out')->where('saldo_type','card')->whereDate('created_at',$from)->orderBy('created_at', 'desc')->with(['siswa','createdBy'])->get();
            return response()->json($transaksi, 200);
        }
        $transaksi = Transaksi::where('mutasi_type','out')->where('saldo_type','card')->whereBetween('created_at',[$from,$to])->orderBy('created_at', 'desc')->with(['siswa','createdBy'])->get();
        return response()->json($transaksi, 200);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $siswa = Siswa::find($request->siswa_id);
        if($siswa){
            if($request->saldo_type == "card"){
                if($request->mutasi_type == "in"){
                    $siswa->saldo_total -= $request->nominal;
                    $siswa->saldo_digitcard += $request->nominal;
                } elseif ($request->mutasi_type == "out") {
                    $siswa->saldo_digitcard -= $request->nominal;
                }
            }elseif ($request->saldo_type == "total") {
                if($request->mutasi_type == "in"){
                    $siswa->saldo_total += $request->nominal;
                } elseif ($request->mutasi_type == "out") {
                    $siswa->saldo_total -= $request->nominal;
                }
            }
            $transaksi = new Transaksi;
            $transaksi->nominal = $request->nominal;
            $transaksi->siswa_id = $request->siswa_id;
            $user = Auth::user();
            // error disini
            $transaksi->created_by = $user->id;
            $transaksi->mutasi_type = $request->mutasi_type;
            $transaksi->saldo_type = $request->saldo_type;
            $siswa->save();
            $transaksi->save();
            $msg="Saldo berhasil di top up";
            return response()->json(['message' => $msg, 'data' => $transaksi], 200);
        } else{
            $msg= "Siswa tidak ditemukan";
            return response()->json(['message' => $msg], 404);
        }
    }

    public function storeDetail(Request $request)
    {   
        $siswa = Siswa::find($request->id);
        $harga_total = 0;
        foreach ($request->detail as $item){
            $harga_total = $item['jumlah_beli'] * $item['harga_jual'];
        };
        if($harga_total > $siswa->saldo_digitcard){
            return response()->json(['message'=>'Saldo tidak mencukupi'], 500);
        }
        $transaksi = new Transaksi;
        $user = Auth::user();
        $transaksi->created_by = $user->id;
        $transaksi->siswa_id = $request->id;
        $transaksi->mutasi_type = 'out';
        $transaksi->saldo_type = 'card';
        $transaksi->nominal = 0;
        $transaksi->save();
        foreach ($request->detail as $item) {
            // return response()->json($item, 200);
            $detail = new TransaksiDetail;
            $produk = Produk::find($item['id']);
            $produk->jumlah -= $item['jumlah_beli'];
            $produk->save();
            $detail->transaksi_id = $transaksi->id;
            $detail->produk_id = $item['id'];
            $detail->jumlah = $item['jumlah_beli'];
            $detail->harga_satuan = $item['harga_jual'];
            $detail->harga_total = $item['jumlah_beli'] * $item['harga_jual'];
            $detail->save();
            $transaksi->nominal += $detail['harga_total'];
        }
        $transaksi->created_by = Auth::user()->id;
        $transaksi->save();
        $siswa->saldo_digitcard -= $transaksi->nominal;
        $siswa->save();
        return response()->json($transaksi, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Transaksi  $transaksi
     * @return \Illuminate\Http\Response
     */
    public function show(Transaksi $transaksi)
    {
        $transaksi->load('detail.produk','siswa','createdBy');
        return response()->json($transaksi, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transaksi  $transaksi
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaksi $transaksi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transaksi  $transaksi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Transaksi  $transaksi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaksi $transaksi)
    {
        //
    }
}
