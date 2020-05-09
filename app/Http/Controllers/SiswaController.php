<?php

namespace App\Http\Controllers;

use App\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Siswa::all();
        return response()->json($data, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $siswa = new Siswa;
        $siswa->nama = $request->nama;
        $siswa->nis = $request->nis;
        $siswa->save();
        return response()->json($siswa, 200,);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $siswa = new Siswa;
        $siswa->nama = $request->nama;
        $siswa->nis = $request->nis;
        $siswa->save();
        return response()->json(['message'=>"Siswa telah ditambahkan",'data'=>$siswa], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Siswa  $siswa
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $siswa = Siswa::where('nis', $request->nis)->first();
        if($siswa){
            $siswa->load(['transaksi' => function ($query) {
                $query->orderBy('id', 'desc');
            }]);
            return response()->json($siswa, 200);
        } else {
            return response()->json(['message'=>'Siswa tidak ditemukan'],400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Siswa  $siswa
     * @return \Illuminate\Http\Response
     */
    public function edit(Siswa $siswa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Siswa  $siswa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Siswa $siswa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Siswa  $siswa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
    }
}
