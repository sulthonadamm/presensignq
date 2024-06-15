<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresensiSeeder extends Seeder
{
    public function run()
    {
        // Define some sample data for seeding
        $niks = DB::table('karyawan')->pluck('nik')->toArray(); // Assuming karyawan table has 'nik' column
        
        // Get the current year and the last 12 months
        $currentYear = date('Y');
        $months = range(1, 12);

        // Iterate over each month and seed two records per month
        foreach ($months as $month) {
            foreach ($niks as $nik) {
                for ($i = 0; $i < 2; $i++) {
                    $date = Carbon::create($currentYear, $month, rand(1, 28)); // Random day in the month
                    $jam_in = Carbon::createFromTime(rand(7, 9), rand(0, 59), 0); // Random time between 7:00 and 9:59
                    $jam_out = (clone $jam_in)->addHours(rand(7, 10)); // Random time 7 to 10 hours after jam_in

                    DB::table('presensi')->insert([
                        'nik' => $nik,
                        'tgl_presensi' => $date->toDateString(),
                        'jam_in' => $jam_in->toTimeString(),
                        'jam_out' => $jam_out->toTimeString(),
                        'foto_in' => 'sample_foto_in.jpg',
                        'foto_out' => 'sample_foto_out.jpg',
                        'lokasi_in' => 'Sample Location In',
                        'lokasi_out' => 'Sample Location Out',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}