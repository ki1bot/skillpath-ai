<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            [
                'programming-fundamentals',
                'Dasar Pemrograman',
                'Fondasi',
                'Memahami variabel, kondisi, perulangan, fungsi, struktur data sederhana, dan cara memecah masalah menjadi langkah program.',
                'Dasar',
            ],
            [
                'git-github',
                'Git & GitHub',
                'Alat',
                'Mengelola perubahan kode dengan commit, branch, merge, remote, dan alur kolaborasi dasar.',
                'Dasar',
            ],
            [
                'terminal-cli',
                'Terminal & CLI',
                'Alat',
                'Bekerja dengan sistem berkas, proses, environment, dan command line secara aman dan efisien.',
                'Dasar',
            ],
            [
                'http-fundamentals',
                'Dasar HTTP',
                'Web',
                'Memahami request, response, method, status code, header, cookie, dan konsep komunikasi client-server.',
                'Dasar',
            ],
            [
                'database-fundamentals',
                'Dasar Basis Data',
                'Data',
                'Memahami tabel, relasi, primary key, foreign key, constraint, indeks, dan prinsip perancangan data relasional.',
                'Dasar',
            ],
            [
                'sql',
                'SQL',
                'Data',
                'Menulis query SELECT, JOIN, agregasi, subquery, insert, update, delete, dan transaksi dasar.',
                'Dasar',
            ],
            [
                'testing-fundamentals',
                'Dasar Pengujian',
                'Kualitas',
                'Memahami perbedaan unit test, integration test, dan feature test serta menulis pengujian untuk perilaku penting aplikasi.',
                'Menengah',
            ],
            [
                'deployment-basics',
                'Dasar Rilis Aplikasi',
                'Rilis',
                'Memahami environment variable, proses build, server, basis data production, HTTPS, dan proses merilis aplikasi.',
                'Menengah',
            ],
            [
                'php-laravel',
                'PHP & Laravel',
                'Backend',
                'Menggunakan PHP modern dan Laravel untuk routing, controller, service, validasi, model, dan dependency injection.',
                'Menengah',
            ],
            [
                'rest-api',
                'REST API',
                'Backend',
                'Merancang endpoint, resource, status code, pagination, filtering, dan kontrak API yang konsisten.',
                'Menengah',
            ],
            [
                'authentication-authorization',
                'Autentikasi & Otorisasi',
                'Backend',
                'Menerapkan identitas pengguna, session atau token, role, permission, dan pembatasan akses resource.',
                'Menengah',
            ],
            [
                'eloquent-orm',
                'Eloquent ORM',
                'Backend',
                'Memodelkan relasi data dan melakukan query dengan Eloquent secara efisien tanpa kehilangan pemahaman SQL.',
                'Menengah',
            ],
            [
                'validation-error-handling',
                'Validasi & Penanganan Kesalahan',
                'Backend',
                'Memvalidasi input, menyusun response kesalahan, dan menangani kondisi gagal secara konsisten.',
                'Menengah',
            ],
            [
                'logging-monitoring',
                'Log & Pemantauan',
                'Backend',
                'Mencatat kejadian penting, membedakan level log, dan menemukan masalah runtime dari informasi yang tersedia.',
                'Menengah',
            ],
            [
                'web-security-basics',
                'Dasar Keamanan Web',
                'Keamanan',
                'Memahami CSRF, XSS, injection, hashing, rate limiting, dan prinsip least privilege.',
                'Menengah',
            ],
            [
                'html-semantics',
                'Semantik HTML',
                'Frontend',
                'Menyusun dokumen dengan elemen semantik yang tepat agar halaman lebih mudah dipahami browser, teknologi bantu, dan pengembang.',
                'Dasar',
            ],
            [
                'css-responsive',
                'CSS & Desain Responsif',
                'Frontend',
                'Membangun tata letak adaptif dengan box model, flexbox, grid, breakpoint, dan pola responsif.',
                'Dasar',
            ],
            [
                'javascript',
                'JavaScript',
                'Frontend',
                'Menguasai tipe data, function, object, array, alur asynchronous, module, dan pengolahan data di browser.',
                'Dasar',
            ],
            [
                'typescript',
                'TypeScript',
                'Frontend',
                'Memodelkan data dengan type, interface, union, generic, narrowing, dan inference untuk mengurangi kesalahan.',
                'Menengah',
            ],
            [
                'react',
                'React',
                'Frontend',
                'Membangun antarmuka berbasis component, props, state, event, effect, form, dan composition.',
                'Menengah',
            ],
            [
                'state-management',
                'Pengelolaan State',
                'Frontend',
                'Memilih state lokal, server state, context, dan pola pengelolaan state tanpa menambah kerumitan yang tidak perlu.',
                'Menengah',
            ],
            [
                'accessibility',
                'Aksesibilitas Web',
                'Frontend',
                'Membuat antarmuka yang dapat digunakan dengan keyboard dan teknologi bantu melalui struktur semantik, focus, label, dan kontras yang tepat.',
                'Menengah',
            ],
            [
                'web-performance',
                'Performa Web',
                'Frontend',
                'Mengurangi biaya render, asset, network, dan JavaScript untuk meningkatkan pengalaman pengguna.',
                'Menengah',
            ],
            [
                'spreadsheet-analysis',
                'Analisis Spreadsheet',
                'Analisis Data',
                'Mengolah data dengan formula, lookup, pivot, filter, dan validasi pada spreadsheet.',
                'Dasar',
            ],
            [
                'statistics-fundamentals',
                'Dasar Statistik',
                'Analisis Data',
                'Memahami ukuran pemusatan, sebaran, distribusi, korelasi, sampling, dan cara membaca hasil statistik sederhana.',
                'Dasar',
            ],
            [
                'data-cleaning',
                'Pembersihan Data',
                'Analisis Data',
                'Menangani missing value, duplikasi, tipe data, outlier, dan inkonsistensi sebelum analisis.',
                'Menengah',
            ],
            [
                'python-data',
                'Python untuk Analisis Data',
                'Analisis Data',
                'Menggunakan Python untuk transformasi data, otomasi analisis, dan eksplorasi dataset.',
                'Menengah',
            ],
            [
                'pandas',
                'Pandas',
                'Analisis Data',
                'Menggunakan DataFrame untuk filtering, grouping, merge, reshape, cleaning, dan agregasi data.',
                'Menengah',
            ],
            [
                'data-visualization',
                'Visualisasi Data',
                'Analisis Data',
                'Memilih visualisasi yang sesuai dan menyampaikan pola data tanpa menyesatkan pembaca.',
                'Menengah',
            ],
            [
                'sql-analytics',
                'SQL untuk Analisis',
                'Analisis Data',
                'Menulis query analitik menggunakan CTE, window function, cohort sederhana, dan metrik berbasis waktu.',
                'Menengah',
            ],
        ];

        foreach ($skills as [$slug, $name, $category, $description, $difficulty]) {
            Skill::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'category' => $category,
                    'description' => $description,
                    'difficulty' => $difficulty,
                ],
            );
        }
    }
}
