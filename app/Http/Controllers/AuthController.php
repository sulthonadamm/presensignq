<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function proseslogin(Request $request){
        // $pass = 123;
        // echo Hash::make($pass);
        if (Auth::guard('karyawan')->attempt(['nik' => $request->nik, 'password' => $request->password]))
        {
            return redirect('/dashboard');
        } else {
            return redirect('/')->with(['warning' => 'nik / password salah']);
        }
    }

    public function proseslogout(){
        if(Auth::guard('karyawan')->check()){
            Auth::guard('karyawan')->logout();
            return redirect('/');
        }
    }
}