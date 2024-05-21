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

    // Hanya menyimpan gambar jika tidak ada kesalahan dan dalam jangkauan
    if ($radius <= 15) {
        // User is within the acceptable range
        if ($cek > 0) {
            $data_pulang = [
                'jam_out' => $jam,
                'foto_out' => $fileName,
                'lokasi_out' => $lokasi
            ];
            $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->update($data_pulang);
    
            if ($update) {
                echo "success|Data presensi berhasil diupdate & jarak anda adalah " . $radius . " meter|out";
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
                echo "success|Data presensi berhasil disimpan & jarak anda adalah " . $radius . " meter|in";
            } else {
                echo "error|Gagal menyimpan data presensi|in";
            }
        }
    } else {
        // User is outside the acceptable range
        echo "error|Anda sedang diluar, jangkauan jarak anda " . $radius . " meter menuju kantor|";
    }
}
    
    //untuk menghitung jarak koordinat
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        // Konversi derajat ke radian
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Haversine formula
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) * sin($dlat / 2) +
            cos($lat1) * cos($lat2) * 
            sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * asin(sqrt($a));

        // Radius Bumi dalam meter
        $r = 6371000;

        // Hitung jarak dalam meter
        $meters = $c * $r;

        return compact('meters');
    }
}
