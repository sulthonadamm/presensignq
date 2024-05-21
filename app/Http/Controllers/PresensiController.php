<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    public function create(){
        $harini = date("Y-m-d");
        $nik = Auth::guard('karyawan')->user()->nik;
        $cek = DB::table('presensi')->where('tgl_presensi', $harini)->where('nik', $nik)->count();
        return view('presensi.create', compact('cek'));
    }
    public function up()
{
    Schema::create('presensi', function (Blueprint $table) {
        $table->bigIncrements('id');
    });
}
public function store(Request $request){
    $nik = Auth::guard('karyawan')->user()->nik;
    $harini = date("Y-m-d");
    $tgl_presensi = date("Y-m-d");
    $jam = Carbon::now('Asia/Jakarta')->format("H:i:s"); // Menggunakan zona waktu Asia/Jakarta (WIB)
    $latitudekantor = -6.895292;
    $longitudekantor = 107.6394147;
    $lokasi = $request->lokasi;
    $lokasiuser = explode(",", $lokasi);
    $latitudeuser = $lokasiuser[0];
    $longitudeuser = $lokasiuser[1];
    $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
    $radius = round($jarak["meters"]);
    $image = $request->image;
    $image_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image));
    $formatName = $nik . "-" . $tgl_presensi . "-" . str_replace(':', '-', $jam);
    $fileName = $formatName . '.png';
    $cek = DB::table('presensi')->where('tgl_presensi', $harini)->where('nik', $nik)->count();

    if ($radius > 15) {
        echo "error|Anda sedang diluar, jangkauan jarak anda " . $radius . " meter menuju kantor|";
        if ($cek > 0) {
            $data_pulang = [
                'jam_out' => $jam,
                'foto_out' => $fileName,
                'lokasi_out' => $lokasi
            ];
            $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->update($data_pulang);

            if ($update) {
                Storage::put('public/absensi/' . $fileName, $image_data);
                echo "success|Data presensi berhasil diupdate|out";
            } else {
                echo "error|Gagal mengupdate data presensi|out";
            }
        } else {
            $data = [
                'nik' => $nik,
                'tgl_presensi' => $tgl_presensi,
                'jam_in' => $jam,
                'foto_in' => $fileName,
                'lokasi_in' => $lokasi
            ];
            $simpan = DB::table('presensi')->insert($data);

            if ($simpan) {
                Storage::put('public/absensi/' . $fileName, $image_data);
                echo "success|Data presensi berhasil disimpan|in";
                
            } else {
                echo "error|Gagal menyimpan data presensi|in";
                
            }
        }
    }
}
    
    //untuk menghitung jarak koordinat
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }
}
