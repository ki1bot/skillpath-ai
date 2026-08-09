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
                'Tools',
                'Mengelola perubahan kode dengan commit, branch, merge, remote, dan workflow kolaborasi dasar.',
                'Dasar',
            ],
            [
                'terminal-cli',
                'Terminal & CLI',
                'Tools',
                'Bekerja dengan filesystem, proses, environment, dan command line secara aman dan efisien.',
                'Dasar',
            ],
            [
                'http-fundamentals',
                'HTTP Fundamentals',
                'Web',
                'Memahami request-response, method, status code, header, cookie, dan konsep client-server.',
                'Dasar',
            ],
            [
                'database-fundamentals',
                'Database Fundamentals',
                'Data',
                'Memahami tabel, relasi, primary key, foreign key, constraint, indeks, dan prinsip desain data relasional.',
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
                'Testing Fundamentals',
                'Quality',
                'Membedakan unit, integration, dan feature test serta menulis pengujian untuk perilaku penting aplikasi.',
                'Menengah',
            ],
            [
                'deployment-basics',
                'Deployment Basics',
                'Delivery',
                'Memahami environment variable, build, server, database production, HTTPS, dan proses rilis aplikasi.',
                'Menengah',
            ],
            [
                'php-laravel',
                'PHP & Laravel',
                'Backend',
                'Menggunakan PHP modern dan Laravel untuk routing, controller, service, validation, model, dan dependency injection.',
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
                'Authentication & Authorization',
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
                'Validation & Error Handling',
                'Backend',
                'Memvalidasi input, menata error response, dan menangani kondisi gagal secara konsisten.',
                'Menengah',
            ],
            [
                'logging-monitoring',
                'Logging & Monitoring',
                'Backend',
                'Mencatat kejadian penting, membedakan level log, dan menemukan masalah runtime dari bukti yang tersedia.',
                'Menengah',
            ],
            [
                'web-security-basics',
                'Web Security Basics',
                'Security',
                'Memahami CSRF, XSS, injection, hashing, rate limiting, dan prinsip least privilege.',
                'Menengah',
            ],
            [
                'html-semantics',
                'HTML Semantics',
                'Frontend',
                'Menyusun struktur dokumen dengan elemen semantik yang tepat untuk aksesibilitas dan maintainability.',
                'Dasar',
            ],
            [
                'css-responsive',
                'CSS & Responsive Design',
                'Frontend',
                'Membangun layout adaptif dengan box model, flexbox, grid, breakpoint, dan pola responsif.',
                'Dasar',
            ],
            [
                'javascript',
                'JavaScript',
                'Frontend',
                'Menguasai tipe data, function, object, array, async flow, module, dan manipulasi data di browser.',
                'Dasar',
            ],
            [
                'typescript',
                'TypeScript',
                'Frontend',
                'Memodelkan data dengan type, interface, union, generic, narrowing, dan inference untuk mengurangi bug.',
                'Menengah',
            ],
            [
                'react',
                'React',
                'Frontend',
                'Membangun UI berbasis component, props, state, event, effect, form, dan composition.',
                'Menengah',
            ],
            [
                'state-management',
                'State Management',
                'Frontend',
                'Memilih state lokal, server state, context, dan pola pengelolaan state tanpa kompleksitas berlebihan.',
                'Menengah',
            ],
            [
                'accessibility',
                'Web Accessibility',
                'Frontend',
                'Membuat UI yang dapat dipakai keyboard dan assistive technology dengan semantic, focus, label, dan contrast yang tepat.',
                'Menengah',
            ],
            [
                'web-performance',
                'Web Performance',
                'Frontend',
                'Mengurangi biaya render, asset, network, dan JavaScript untuk memperbaiki pengalaman pengguna.',
                'Menengah',
            ],
            [
                'spreadsheet-analysis',
                'Spreadsheet Analysis',
                'Data Analyst',
                'Mengolah data dengan formula, lookup, pivot, filter, dan validasi pada spreadsheet.',
                'Dasar',
            ],
            [
                'statistics-fundamentals',
                'Statistics Fundamentals',
                'Data Analyst',
                'Memahami ukuran pemusatan, sebaran, distribusi, korelasi, sampling, dan interpretasi sederhana.',
                'Dasar',
            ],
            [
                'data-cleaning',
                'Data Cleaning',
                'Data Analyst',
                'Menangani missing value, duplikasi, tipe data, outlier, dan inkonsistensi sebelum analisis.',
                'Menengah',
            ],
            [
                'python-data',
                'Python for Data Analysis',
                'Data Analyst',
                'Menggunakan Python untuk transformasi data, otomasi analisis, dan eksplorasi dataset.',
                'Menengah',
            ],
            [
                'pandas',
                'Pandas',
                'Data Analyst',
                'Menggunakan DataFrame untuk filtering, grouping, merge, reshape, cleaning, dan agregasi data.',
                'Menengah',
            ],
            [
                'data-visualization',
                'Data Visualization',
                'Data Analyst',
                'Memilih visual yang tepat dan menyampaikan pola data tanpa menyesatkan pembaca.',
                'Menengah',
            ],
            [
                'sql-analytics',
                'SQL Analytics',
                'Data Analyst',
                'Menulis query analitik dengan CTE, window function, cohort sederhana, dan metrik berbasis waktu.',
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
