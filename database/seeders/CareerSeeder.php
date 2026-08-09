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
                'name' => 'Pengembang Backend',
                'slug' => 'backend-developer',
                'tagline' => 'Bangun logika, API, dan fondasi data yang membuat aplikasi bekerja dengan baik.',
                'description' => 'Pengembang backend menangani logika aplikasi, pengolahan data, keamanan, integrasi, dan layanan yang digunakan oleh frontend maupun aplikasi lain.',
                'responsibilities' => [
                    'Merancang API yang konsisten',
                    'Mengelola basis data dan transaksi',
                    'Menerapkan autentikasi serta otorisasi',
                    'Menjaga kualitas melalui pengujian, pencatatan log, dan proses rilis',
                ],
                'difficulty' => 'Menengah',
                'accent' => '#C7FF5E',
                'is_active' => true,
            ],
            [
                'name' => 'Pengembang Frontend',
                'slug' => 'frontend-developer',
                'tagline' => 'Ubah kebutuhan produk menjadi antarmuka yang cepat, jelas, dan nyaman digunakan.',
                'description' => 'Pengembang frontend membangun pengalaman pengguna di browser dengan perhatian pada struktur halaman, responsivitas, aksesibilitas, state aplikasi, performa, dan integrasi API.',
                'responsibilities' => [
                    'Menerjemahkan desain menjadi antarmuka responsif',
                    'Mengelola state dan interaksi pengguna',
                    'Mengintegrasikan API',
                    'Menjaga aksesibilitas dan performa antarmuka',
                ],
                'difficulty' => 'Menengah',
                'accent' => '#79D7FF',
                'is_active' => true,
            ],
            [
                'name' => 'Analis Data',
                'slug' => 'data-analyst',
                'tagline' => 'Ubah data mentah menjadi temuan yang membantu pengambilan keputusan.',
                'description' => 'Analis data membersihkan, mengolah, mengeksplorasi, dan memvisualisasikan data untuk menjawab pertanyaan bisnis dengan cara yang dapat dijelaskan.',
                'responsibilities' => [
                    'Membersihkan dan memvalidasi data',
                    'Menulis query analitik',
                    'Menerapkan statistik dasar',
                    'Menyusun visualisasi dan temuan yang dapat ditindaklanjuti',
                ],
                'difficulty' => 'Menengah',
                'accent' => '#FFD95A',
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
