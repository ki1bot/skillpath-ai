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
                'name' => 'Sistem Informasi',
                'slug' => 'sistem-informasi',
                'tagline' => 'Belajar menghubungkan data, proses bisnis, sistem, dan kebutuhan pengguna.',
                'description' => 'Di Sistem Informasi, kemampuanmu dipetakan melalui tiga bidang utama: Analisis Data, Pengembangan Sistem, dan UI/UX. Setiap bidang memiliki tiga kemampuan, sehingga ada sembilan kemampuan yang akan dinilai dan dikembangkan.',
                'responsibilities' => [
                    'Analisis Data',
                    'Pengembangan Sistem',
                    'UI/UX',
                ],
                'difficulty' => 'Menengah',
                'accent' => '#79D7FF',
                'is_active' => true,
            ],
            [
                'name' => 'Manajemen',
                'slug' => 'manajemen',
                'tagline' => 'Belajar memahami pasar, mengelola keuangan, dan mengembangkan orang di dalam organisasi.',
                'description' => 'Di Manajemen, kemampuanmu dipetakan melalui Marketing, Keuangan, dan Human Resources. Ketiga bidang ini membantu melihat kemampuanmu dalam memahami pasar, mengambil keputusan keuangan, dan mengelola sumber daya manusia.',
                'responsibilities' => [
                    'Marketing',
                    'Keuangan',
                    'Human Resources',
                ],
                'difficulty' => 'Menengah',
                'accent' => '#FFD95A',
                'is_active' => true,
            ],
            [
                'name' => 'Teknik Informatika',
                'slug' => 'teknik-informatika',
                'tagline' => 'Bangun dasar pemrograman, pahami sistem komputer, lalu kenali penerapan kecerdasan buatan.',
                'description' => 'Di Teknik Informatika, kemampuanmu dipetakan melalui Pemrograman dan Rekayasa Perangkat Lunak, Jaringan dan Sistem Komputer, serta Artificial Intelligence. Total ada sembilan kemampuan yang digunakan sebagai dasar asesmen dan jalur belajarmu.',
                'responsibilities' => [
                    'Pemrograman dan Rekayasa Perangkat Lunak',
                    'Jaringan dan Sistem Komputer',
                    'Artificial Intelligence',
                ],
                'difficulty' => 'Menengah',
                'accent' => '#C7FF5E',
                'is_active' => true,
            ],
            [
                'name' => 'Sistem Komputer',
                'slug' => 'sistem-komputer',
                'tagline' => 'Pahami arsitektur komputer, embedded system, IoT, jaringan, dan keamanan komputer.',
                'description' => 'Di Sistem Komputer, kemampuanmu dipetakan melalui Arsitektur dan Organisasi Komputer, Embedded System dan Internet of Things, serta Jaringan dan Keamanan Komputer. Setiap bidang memiliki tiga kemampuan, sehingga ada sembilan kemampuan yang digunakan sebagai dasar asesmen dan jalur belajarmu.',
                'responsibilities' => [
                    'Arsitektur dan Organisasi Komputer',
                    'Embedded System dan Internet of Things',
                    'Jaringan dan Keamanan Komputer',
                ],
                'difficulty' => 'Menengah',
                'accent' => '#FF9F68',
                'is_active' => true,
            ],
            [
                'name' => 'Psikologi',
                'slug' => 'psikologi',
                'tagline' => 'Pahami perilaku manusia di organisasi, dalam konseling, dan melalui penelitian.',
                'description' => 'Di Psikologi, kemampuanmu dipetakan melalui Psikologi Industri dan Organisasi, Konseling, dan Penelitian Psikologi. Setiap bidang memiliki tiga kemampuan yang membantu melihat pemahamanmu tentang perilaku manusia dan proses penelitian.',
                'responsibilities' => [
                    'Psikologi Industri dan Organisasi',
                    'Konseling',
                    'Penelitian Psikologi',
                ],
                'difficulty' => 'Menengah',
                'accent' => '#FF8FAB',
                'is_active' => true,
            ],
            [
                'name' => 'Ilmu Komunikasi',
                'slug' => 'ilmu-komunikasi',
                'tagline' => 'Pelajari cara membangun hubungan, menyampaikan berita, dan membuat konten digital.',
                'description' => 'Di Ilmu Komunikasi, kemampuanmu dipetakan melalui Public Relations, Jurnalistik, dan Digital Media. Sembilan kemampuan di dalamnya digunakan untuk melihat bagaimana kamu memahami komunikasi, penyampaian informasi, dan produksi media.',
                'responsibilities' => [
                    'Public Relations',
                    'Jurnalistik',
                    'Digital Media',
                ],
                'difficulty' => 'Menengah',
                'accent' => '#C4B5FD',
                'is_active' => true,
            ],
        ];

        foreach ($careers as $career) {
            Career::updateOrCreate(
                [
                    'slug' => $career['slug'],
                ],
                $career,
            );
        }
    }
}
