<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile\getClientOriginalExtention;

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
    // if ($radius > 20) {
    //     return "error|Jarak Anda terlalu jauh dari kantor, ($radius)|";
    // }
    $cek = DB::table('presensi')->where('tgl_presensi', $harini)->where('nik', $nik)->count();
    if($cek > 0){
        $ket = "out";
    } else {
        $ket = "in";
    }
    $image = $request->image;
    $image_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image));
    $formatName = $nik . "-" . $tgl_presensi . "-" . $ket;
    $fileName = $formatName . '.png';
    if ($cek > 0) {
        $data_pulang = [
            'jam_out' => $jam,
            'foto_out' => $fileName,
            'lokasi_out' => $lokasi
        ];             $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->update($data_pulang);
          if ($update) {
            Storage::put('public/absensi/' . $fileName, $image_data);
                echo "success|Data presensi berhasil diupdate|out";
        } else {
            echo 1;
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
            echo "success|Terima Kasih, Selamat Bekerja|in";
            Storage::put('public/absensi/' . $fileName, $image_data);
        } else {
            echo 1;
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

    public function editprofile(){
    
        $nik = Auth::guard('karyawan')->user()->nik;
        $karyawan = DB::table('karyawan')->where('nik', $nik) ->first();
        return view('presensi.editprofile', compact('karyawan'));
    }

    public function updateprofile(Request $request) {
        $nik = Auth::guard('karyawan')->user()->nik;
        $nama_lengkap = $request->nama_lengkap;
        $no_hp = $request->no_hp;
    
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
    
        if ($request->hasFile('foto')) {
            $foto = $nik . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $karyawan->foto;
        }
    
        $data = [
            'nama_lengkap' => $nama_lengkap,
            'no_hp' => $no_hp,
            'foto' => $foto
        ];
    
        if ($request->has('password') && !empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }
    
        $update = DB::table('karyawan')->where('nik', $nik)->update($data);
        if($update){
            if($request->hasFile('foto')){
                $folderPath = "public/karyawan/";
                $request->file('foto')->storeAs($folderPath, $foto);
            }
            return Redirect::back()->with(['success' => 'Data Berhasil di Update!']);
        } else {
            return redirect::back()->with(['error' => 'Data Gagal di Update!']);
        }
    }

    public function histori(){
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.histori', compact('namabulan'));
    }

    public function gethistori(Request $request){
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $nik = Auth::guard('karyawan')->user()->nik;
        $histori = DB::table('presensi')
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->where('nik', $nik)
            ->orderBy('tgl_presensi')
            ->get();

        return view ('presensi.gethistori', compact('histori'));
    }
}