<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('karyawan')->insert([
            [
                'nik' => '12345',
                'nama_lengkap' => 'Im as Admin',
                'jabatan' => 'Employee',
                'no_hp' => '081234567890',
                'password' => bcrypt('123'),
                'remember_token' => Str::random(10),
            ],
            // Tambahkan data karyawan lainnya di sini
        ]);
    }
}
