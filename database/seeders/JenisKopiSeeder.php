<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JenisKopi;

class JenisKopiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JenisKopi::create(['jenis_kopi' => 'Arabica']);
        JenisKopi::create(['jenis_kopi' => 'Robusta']);
    }
}
