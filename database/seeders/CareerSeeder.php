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
                'tagline' => 'Pelajari data, pengembangan sistem, dan pengalaman pengguna dalam satu jalur yang saling terhubung.',
                'description' => 'Sistem Informasi menggabungkan teknologi, proses bisnis, data, dan kebutuhan pengguna. SkillPath menilai kemampuan pada Analisis Data, Pengembangan Sistem, dan UI/UX.',
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
                'tagline' => 'Pelajari bagaimana pemasaran, keuangan, dan sumber daya manusia membantu organisasi berkembang.',
                'description' => 'Manajemen berfokus pada proses pengambilan keputusan dan pengelolaan organisasi. SkillPath menilai kemampuan pada Marketing, Keuangan, dan Human Resources.',
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
                'tagline' => 'Bangun dasar yang kuat dalam pemrograman, sistem komputer, jaringan, dan kecerdasan buatan.',
                'description' => 'Teknik Informatika berfokus pada ilmu komputasi dan pengembangan teknologi. SkillPath menilai kemampuan pada Rekayasa Perangkat Lunak, Jaringan dan Sistem Komputer, serta Artificial Intelligence.',
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
                'name' => 'Psikologi',
                'slug' => 'psikologi',
                'tagline' => 'Pahami perilaku manusia melalui organisasi, konseling, dan penelitian psikologi.',
                'description' => 'Psikologi mempelajari perilaku dan proses mental manusia. SkillPath menilai kemampuan pada Psikologi Industri dan Organisasi, Konseling, dan Penelitian Psikologi.',
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
                'tagline' => 'Kembangkan kemampuan komunikasi melalui public relations, jurnalistik, dan media digital.',
                'description' => 'Ilmu Komunikasi mempelajari bagaimana pesan dibuat, disampaikan, dan diterima. SkillPath menilai kemampuan pada Public Relations, Jurnalistik, dan Digital Media.',
                'responsibilities' => [
                    'Public Relations',
                    'Jurnalistik',
                    'Digital Media',
                ],
                'difficulty' => 'Menengah',
                'accent' => '#C4B5FD',
                'is_active' => true,
            ],
            [
                'name' => 'Pengembang Backend',
                'slug' => 'backend-developer',
                'tagline' => 'Data lama SkillPath.',
                'description' => 'Data lama yang dipertahankan untuk kompatibilitas histori sistem.',
                'responsibilities' => [],
                'difficulty' => 'Legacy',
                'accent' => '#C7FF5E',
                'is_active' => false,
            ],
            [
                'name' => 'Pengembang Frontend',
                'slug' => 'frontend-developer',
                'tagline' => 'Data lama SkillPath.',
                'description' => 'Data lama yang dipertahankan untuk kompatibilitas histori sistem.',
                'responsibilities' => [],
                'difficulty' => 'Legacy',
                'accent' => '#79D7FF',
                'is_active' => false,
            ],
            [
                'name' => 'Analis Data',
                'slug' => 'data-analyst',
                'tagline' => 'Data lama SkillPath.',
                'description' => 'Data lama yang dipertahankan untuk kompatibilitas histori sistem.',
                'responsibilities' => [],
                'difficulty' => 'Legacy',
                'accent' => '#FFD95A',
                'is_active' => false,
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
