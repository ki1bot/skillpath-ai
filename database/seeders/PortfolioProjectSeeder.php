<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\PortfolioProject;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PortfolioProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'backend-developer',
                'Task Management API',
                'Pemula',
                10,
                'Bangun API pengelolaan tugas yang benar-benar punya autentikasi dan validasi.',
                'Mahasiswa membutuhkan API sederhana untuk mengelola tugas pribadi secara aman.',
                [
                    'Registrasi dan login',
                    'CRUD tugas',
                    'Status tugas',
                    'Validasi input',
                    'Relasi user dan task',
                ],
                [
                    'Filter dan pagination',
                    'Activity log',
                ],
                [
                    'Endpoint sesuai kontrak',
                    'User hanya mengakses tugas sendiri',
                    'Validation error konsisten',
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
                'Sistem Reservasi Ruangan API',
                'Menengah',
                18,
                'Bangun backend reservasi dengan aturan bentrok jadwal dan role.',
                'Organisasi membutuhkan layanan reservasi ruangan yang menolak jadwal bentrok dan membedakan hak akses.',
                [
                    'Manajemen user',
                    'Jadwal ruangan',
                    'Pemeriksaan bentrok',
                    'Role admin dan user',
                    'Riwayat reservasi',
                ],
                [
                    'Notifikasi',
                    'Audit log',
                    'Dokumentasi OpenAPI',
                ],
                [
                    'Tidak ada double booking',
                    'Hak akses diuji',
                    'Transaksi data aman',
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
                'Personal Finance Dashboard',
                'Pemula',
                12,
                'Bangun dashboard keuangan yang tetap terbaca baik di mobile dan desktop.',
                'Pengguna membutuhkan ringkasan transaksi yang cepat dipahami tanpa tabel yang membingungkan.',
                [
                    'Daftar transaksi',
                    'Filter kategori',
                    'Ringkasan pemasukan dan pengeluaran',
                    'Chart bulanan',
                    'Responsive layout',
                ],
                [
                    'Dark mode',
                    'Export tampilan',
                ],
                [
                    'Keyboard navigation berfungsi',
                    'State filter konsisten',
                    'Layout responsif',
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
                'Accessible Event Planner',
                'Menengah',
                18,
                'Bangun antarmuka pengelolaan event dengan form, state, dan pola aksesibilitas yang serius.',
                'Panitia membutuhkan UI untuk membuat event dan mengelola peserta tanpa membingungkan pengguna keyboard.',
                [
                    'Form event',
                    'Daftar peserta',
                    'Filter status',
                    'Dialog konfirmasi',
                    'Validasi UI',
                ],
                [
                    'Optimistic update',
                    'Skeleton loading',
                ],
                [
                    'Focus management benar',
                    'Error form jelas',
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
                'Analisis Kinerja Penjualan',
                'Pemula',
                10,
                'Bersihkan dan analisis data penjualan lalu rangkum temuan yang relevan.',
                'Manajer ingin mengetahui kategori, wilayah, dan periode yang paling berpengaruh pada penjualan.',
                [
                    'Data cleaning',
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
                    'Setiap chart menjawab pertanyaan',
                    'Transformasi data terdokumentasi',
                    'Insight didukung angka',
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
                'Customer Retention Analysis',
                'Menengah',
                18,
                'Gunakan SQL dan Python untuk melihat pola pelanggan kembali dan perubahan antar periode.',
                'Tim produk ingin memahami apakah pelanggan kembali bertransaksi setelah pembelian pertama.',
                [
                    'Definisi cohort',
                    'Query transaksi',
                    'Retention table',
                    'Visualisasi retention',
                    'Ringkasan insight',
                ],
                [
                    'Segmentasi kanal',
                    'Perbandingan campaign',
                ],
                [
                    'Definisi metrik jelas',
                    'Query dapat direproduksi',
                    'Kesimpulan tidak melebihi data',
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
                ['slug' => Str::slug($title)],
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
