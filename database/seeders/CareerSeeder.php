<?php

namespace Database\Seeders;

use App\Models\Career;
use Illuminate\Database\Seeder;

class CareerSeeder extends Seeder
{
    public function run(): void
    {
        $careers = [
            [
                'name' => 'Backend Developer',
                'slug' => 'backend-developer',
                'tagline' => 'Bangun logika, API, dan fondasi data yang membuat produk benar-benar bekerja.',
                'description' => 'Backend Developer menangani logika aplikasi, pengolahan data, keamanan, integrasi, dan layanan yang dipakai frontend maupun aplikasi lain.',
                'responsibilities' => [
                    'Merancang API yang konsisten',
                    'Mengelola database dan transaksi',
                    'Menerapkan autentikasi serta otorisasi',
                    'Menjaga kualitas melalui testing, logging, dan deployment',
                ],
                'difficulty' => 'Menengah',
                'accent' => '#C7FF5E',
                'is_active' => true,
            ],
            [
                'name' => 'Frontend Developer',
                'slug' => 'frontend-developer',
                'tagline' => 'Ubah kebutuhan produk menjadi antarmuka yang cepat, jelas, dan nyaman digunakan.',
                'description' => 'Frontend Developer membangun pengalaman pengguna di browser dengan perhatian pada struktur, responsivitas, aksesibilitas, state, performa, dan integrasi API.',
                'responsibilities' => [
                    'Menerjemahkan desain menjadi UI responsif',
                    'Mengelola state dan interaksi pengguna',
                    'Mengintegrasikan API',
                    'Menjaga aksesibilitas dan performa antarmuka',
                ],
                'difficulty' => 'Menengah',
                'accent' => '#79D7FF',
                'is_active' => true,
            ],
            [
                'name' => 'Data Analyst',
                'slug' => 'data-analyst',
                'tagline' => 'Ubah data mentah menjadi temuan yang bisa dipakai untuk mengambil keputusan.',
                'description' => 'Data Analyst membersihkan, mengolah, mengeksplorasi, dan memvisualisasikan data untuk menjawab pertanyaan bisnis dengan metode yang dapat dijelaskan.',
                'responsibilities' => [
                    'Membersihkan dan memvalidasi data',
                    'Menulis query analitik',
                    'Menerapkan statistik dasar',
                    'Menyusun visualisasi dan insight yang dapat ditindaklanjuti',
                ],
                'difficulty' => 'Menengah',
                'accent' => '#FFCE5C',
                'is_active' => true,
            ],
        ];

        foreach ($careers as $career) {
            Career::updateOrCreate(
                ['slug' => $career['slug']],
                $career,
            );
        }
    }
}
