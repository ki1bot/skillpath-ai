<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\PortfolioProject;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class PortfolioProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'backend-developer',
                'task-management-api',
                'API Pengelolaan Tugas',
                'Pemula',
                10,
                'Bangun API pengelolaan tugas dengan autentikasi, validasi, dan aturan akses yang jelas.',
                'Pengguna membutuhkan API sederhana untuk mengelola tugas pribadi secara aman.',
                [
                    'Registrasi dan login',
                    'Tambah, lihat, ubah, dan hapus tugas',
                    'Status tugas',
                    'Validasi input',
                    'Relasi pengguna dan tugas',
                ],
                [
                    'Filter dan pagination',
                    'Log aktivitas',
                ],
                [
                    'Endpoint sesuai kontrak',
                    'Pengguna hanya dapat mengakses tugas miliknya',
                    'Pesan kesalahan validasi konsisten',
                ],
                [
                    'rest-api' => 55,
                    'database-fundamentals' => 55,
                    'authentication-authorization' => 50,
                    'validation-error-handling' => 45,
                ],
            ],
            [
                'backend-developer',
                'sistem-reservasi-ruangan-api',
                'API Reservasi Ruangan',
                'Menengah',
                18,
                'Bangun backend reservasi ruangan dengan pemeriksaan bentrok jadwal dan pembagian hak akses.',
                'Organisasi membutuhkan layanan reservasi ruangan yang menolak jadwal bentrok dan membedakan hak akses administrator dengan pengguna.',
                [
                    'Manajemen pengguna',
                    'Jadwal ruangan',
                    'Pemeriksaan bentrok',
                    'Hak akses administrator dan pengguna',
                    'Riwayat reservasi',
                ],
                [
                    'Notifikasi',
                    'Log audit',
                    'Dokumentasi OpenAPI',
                ],
                [
                    'Tidak terjadi pemesanan ganda',
                    'Hak akses telah diuji',
                    'Transaksi data berjalan aman',
                ],
                [
                    'rest-api' => 70,
                    'sql' => 65,
                    'authentication-authorization' => 65,
                    'testing-fundamentals' => 55,
                ],
            ],
            [
                'frontend-developer',
                'personal-finance-dashboard',
                'Dashboard Keuangan Pribadi',
                'Pemula',
                12,
                'Bangun dasboard keuangan yang tetap mudah dibaca di perangkat mobile maupun desktop.',
                'Pengguna membutuhkan ringkasan transaksi yang cepat dipahami tanpa tabel yang membingungkan.',
                [
                    'Daftar transaksi',
                    'Filter kategori',
                    'Ringkasan pemasukan dan pengeluaran',
                    'Grafik bulanan',
                    'Tata letak responsif',
                ],
                [
                    'Mode gelap',
                    'Ekspor tampilan',
                ],
                [
                    'Navigasi keyboard berfungsi',
                    'State filter tetap konsisten',
                    'Tata letak responsif',
                ],
                [
                    'react' => 60,
                    'typescript' => 50,
                    'css-responsive' => 60,
                    'accessibility' => 45,
                ],
            ],
            [
                'frontend-developer',
                'accessible-event-planner',
                'Pengelola Acara Aksesibel',
                'Menengah',
                18,
                'Bangun antarmuka pengelolaan acara dengan form, state, dan aksesibilitas yang serius.',
                'Panitia membutuhkan antarmuka untuk membuat acara dan mengelola peserta yang tetap nyaman digunakan dengan keyboard.',
                [
                    'Form acara',
                    'Daftar peserta',
                    'Filter status',
                    'Dialog konfirmasi',
                    'Validasi antarmuka',
                ],
                [
                    'Pembaruan optimistis',
                    'Tampilan sementara saat memuat data',
                ],
                [
                    'Pengelolaan focus berjalan benar',
                    'Pesan kesalahan form jelas',
                    'State tidak hilang tanpa alasan',
                ],
                [
                    'react' => 70,
                    'state-management' => 60,
                    'accessibility' => 65,
                    'testing-fundamentals' => 50,
                ],
            ],
            [
                'data-analyst',
                'analisis-kinerja-penjualan',
                'Analisis Kinerja Penjualan',
                'Pemula',
                10,
                'Bersihkan dan analisis data penjualan lalu rangkum temuan yang benar-benar relevan.',
                'Manajer ingin mengetahui kategori, wilayah, dan periode yang paling berpengaruh terhadap penjualan.',
                [
                    'Pembersihan data',
                    'Ringkasan KPI',
                    'Analisis kategori',
                    'Analisis waktu',
                    'Visualisasi utama',
                ],
                [
                    'Segmentasi pelanggan',
                    'Analisis margin',
                ],
                [
                    'Setiap grafik menjawab pertanyaan',
                    'Transformasi data terdokumentasi',
                    'Temuan didukung oleh angka',
                ],
                [
                    'data-cleaning' => 55,
                    'statistics-fundamentals' => 50,
                    'data-visualization' => 55,
                    'spreadsheet-analysis' => 50,
                ],
            ],
            [
                'data-analyst',
                'customer-retention-analysis',
                'Analisis Retensi Pelanggan',
                'Menengah',
                18,
                'Gunakan SQL dan Python untuk melihat pola pelanggan yang kembali serta perubahan antarperiode.',
                'Tim produk ingin memahami apakah pelanggan kembali bertransaksi setelah pembelian pertama.',
                [
                    'Definisi cohort',
                    'Query transaksi',
                    'Tabel retensi',
                    'Visualisasi retensi',
                    'Ringkasan temuan',
                ],
                [
                    'Segmentasi kanal',
                    'Perbandingan kampanye',
                ],
                [
                    'Definisi metrik jelas',
                    'Query dapat dijalankan kembali',
                    'Kesimpulan tidak melebihi data yang tersedia',
                ],
                [
                    'sql-analytics' => 65,
                    'pandas' => 60,
                    'statistics-fundamentals' => 60,
                    'data-visualization' => 60,
                ],
            ],
        ];

        $skills = Skill::query()
            ->get()
            ->keyBy('slug');

        foreach ($projects as [
            $careerSlug,
            $slug,
            $title,
            $difficulty,
            $hours,
            $summary,
            $problem,
            $minimum,
            $stretch,
            $criteria,
            $requiredSkills,
        ]) {
            $career = Career::query()
                ->where('slug', $careerSlug)
                ->firstOrFail();

            $project = PortfolioProject::updateOrCreate(
                ['slug' => $slug],
                [
                    'career_id' => $career->id,
                    'title' => $title,
                    'summary' => $summary,
                    'problem_statement' => $problem,
                    'difficulty' => $difficulty,
                    'minimum_features' => $minimum,
                    'stretch_features' => $stretch,
                    'completion_criteria' => $criteria,
                    'estimated_hours' => $hours,
                ],
            );

            $sync = [];

            foreach ($requiredSkills as $skillSlug => $level) {
                $skill = $skills->get($skillSlug);

                if (! $skill) {
                    continue;
                }

                $sync[$skill->id] = [
                    'required_level' => $level,
                    'weight' => 1,
                ];
            }

            $project->skills()->sync($sync);
        }
    }
}
