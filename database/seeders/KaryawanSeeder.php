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
                'nik' => '123',
                'nama_lengkap' => 'Admin',
                'jabatan' => 'Administrator',
                'no_hp' => '081234567890',
                'foto' => '',
                'password' => bcrypt('123'),
                'remember_token' => Str::random(10),
            ],
            [
                'nik' => '234',
                'nama_lengkap' => 'Sulthon Adam Maulana',
                'jabatan' => 'Administrator',
                'no_hp' => '081234567890',
                'foto' => '',
                'password' => bcrypt('123'),
                'remember_token' => Str::random(10),
            ],
            [
                'nik' => '12345',
                'nama_lengkap' => 'Aguh Prayoga',
                'jabatan' => 'Administrator',
                'no_hp' => '081234567890',
                'foto' => '',
                'password' => bcrypt('123'),
                'remember_token' => Str::random(10),
            ],
            [
                'nik' => '23456',
                'nama_lengkap' => 'Rian Khoirulloh',
                'jabatan' => 'Administrator',
                'no_hp' => '081234567890',
                'foto' => '',
                'password' => bcrypt('123'),
                'remember_token' => Str::random(10),
            ],
            [
                'nik' => '321',
                'nama_lengkap' => 'Qineb Abiyyi',
                'jabatan' => 'Administrator',
                'no_hp' => '081234567890',
                'foto' => '',
                'password' => bcrypt('321'),
                'remember_token' => Str::random(10),
            ],
            // Tambahkan data karyawan lainnya di sini
        ]);
    }
}
