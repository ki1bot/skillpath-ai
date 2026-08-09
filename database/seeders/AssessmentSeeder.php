<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\Career;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $sets = [
            'backend-developer' => [
                'programming-fundamentals' => [
                    'Apa keluaran yang paling mungkin dari kondisi if ketika ekspresinya bernilai false?',
                    [
                        'Blok if tetap dijalankan',
                        'Blok if dilewati',
                        'Program selalu berhenti',
                        'Database dihapus',
                    ],
                    'B',
                ],
                'git-github' => [
                    'Anda ingin menyimpan snapshot perubahan lokal sebelum push. Langkah yang tepat?',
                    [
                        'git commit setelah staging',
                        'git clone setiap kali',
                        'git reset --hard selalu',
                        'hapus folder .git',
                    ],
                    'A',
                ],
                'http-fundamentals' => [
                    'Client mengirim data untuk membuat user baru. Method yang paling tepat?',
                    ['GET', 'POST', 'HEAD', 'OPTIONS'],
                    'B',
                ],
                'database-fundamentals' => [
                    'Satu user dapat memiliki banyak task. Relasi yang paling tepat?',
                    [
                        'One-to-many',
                        'One-to-one wajib',
                        'Tidak perlu relasi',
                        'Many-to-many selalu',
                    ],
                    'A',
                ],
                'sql' => [
                    'Anda ingin mengambil task beserta nama user dari dua tabel. Konsep SQL yang dibutuhkan?',
                    ['JOIN', 'TRUNCATE', 'DROP DATABASE', 'GRANT saja'],
                    'A',
                ],
                'php-laravel' => [
                    'Di Laravel, route biasanya meneruskan request ke?',
                    [
                        'Controller atau handler',
                        'File gambar',
                        'CSS variable',
                        'Git branch',
                    ],
                    'A',
                ],
                'eloquent-orm' => [
                    'User::with("projects")->get() terutama digunakan untuk?',
                    [
                        'Eager loading relasi',
                        'Menghapus user',
                        'Membuat migration',
                        'Mengubah password server',
                    ],
                    'A',
                ],
                'rest-api' => [
                    'Endpoint DELETE /tasks/15 berhasil menghapus data tanpa body respons. Status yang cocok?',
                    ['204', '404', '500', '101'],
                    'A',
                ],
                'authentication-authorization' => [
                    'User sudah login tetapi bukan admin. Pemeriksaan akses ke halaman admin termasuk?',
                    [
                        'Authorization',
                        'Authentication saja',
                        'Caching',
                        'Migration',
                    ],
                    'A',
                ],
                'deployment-basics' => [
                    'Secret production sebaiknya?',
                    [
                        'Disimpan di environment/secret manager',
                        'Ditaruh di repository publik',
                        'Ditulis di frontend bundle',
                        'Dikirim ke semua user',
                    ],
                    'A',
                ],
            ],
            'frontend-developer' => [
                'html-semantics' => [
                    'Bagian navigasi utama sebaiknya menggunakan elemen?',
                    ['nav', 'strong', 'canvas', 'code'],
                    'A',
                ],
                'css-responsive' => [
                    'Untuk layout kartu yang berubah kolom berdasarkan ruang, alat CSS yang relevan?',
                    [
                        'Grid atau Flexbox',
                        'SQL JOIN',
                        'Hashing password',
                        'Cron',
                    ],
                    'A',
                ],
                'javascript' => [
                    'await digunakan untuk?',
                    [
                        'Menunggu Promise pada async function',
                        'Menulis CSS',
                        'Membuat tabel SQL',
                        'Mengganti HTML menjadi gambar',
                    ],
                    'A',
                ],
                'typescript' => [
                    'Type union "draft" | "published" membantu karena?',
                    [
                        'Membatasi nilai status yang valid',
                        'Menghapus runtime',
                        'Membuat API otomatis',
                        'Mengganti database',
                    ],
                    'A',
                ],
                'react' => [
                    'Props pada React terutama digunakan untuk?',
                    [
                        'Mengirim data ke component',
                        'Membuat database',
                        'Menyimpan secret',
                        'Menjalankan migration',
                    ],
                    'A',
                ],
                'state-management' => [
                    'Filter yang hanya dipakai satu komponen paling sederhana disimpan di?',
                    [
                        'Local state',
                        'Global state wajib',
                        'Database production',
                        'DNS',
                    ],
                    'A',
                ],
                'accessibility' => [
                    'Tombol custom harus tetap dapat digunakan dengan?',
                    [
                        'Keyboard',
                        'Mouse saja',
                        'Resolusi 4K saja',
                        'Admin saja',
                    ],
                    'A',
                ],
                'http-fundamentals' => [
                    'Status 401 biasanya berkaitan dengan?',
                    [
                        'Kebutuhan autentikasi',
                        'CSS rusak',
                        'Query sukses',
                        'File terlalu kecil',
                    ],
                    'A',
                ],
                'testing-fundamentals' => [
                    'Test UI yang baik seharusnya memeriksa?',
                    [
                        'Perilaku yang dilihat pengguna',
                        'Nama variabel internal saja',
                        'Warna editor',
                        'Jumlah commit',
                    ],
                    'A',
                ],
                'web-performance' => [
                    'Gambar sangat besar di hero membuat halaman lambat. Tindakan awal yang masuk akal?',
                    [
                        'Optimalkan ukuran dan format',
                        'Tambah JavaScript acak',
                        'Hapus semantic HTML',
                        'Duplikasi gambar',
                    ],
                    'A',
                ],
            ],
            'data-analyst' => [
                'spreadsheet-analysis' => [
                    'Anda ingin merangkum penjualan berdasarkan kategori dengan cepat. Fitur yang cocok?',
                    ['Pivot table', 'CSS Grid', 'Git merge', 'JWT'],
                    'A',
                ],
                'statistics-fundamentals' => [
                    'Dataset memiliki satu nilai ekstrem sangat besar. Ukuran pusat yang lebih tahan?',
                    ['Median', 'Mean selalu', 'Range', 'Nama kolom'],
                    'A',
                ],
                'data-cleaning' => [
                    'Sebelum menghapus baris duplikat, hal pertama yang perlu dipastikan?',
                    [
                        'Definisi duplikat sesuai konteks data',
                        'Warna chart',
                        'Nama repository',
                        'Versi CSS',
                    ],
                    'A',
                ],
                'database-fundamentals' => [
                    'Primary key digunakan untuk?',
                    [
                        'Mengidentifikasi baris secara unik',
                        'Menggambar chart',
                        'Menyimpan warna',
                        'Mengirim email',
                    ],
                    'A',
                ],
                'sql' => [
                    'SUM dan COUNT termasuk?',
                    [
                        'Aggregate function',
                        'CSS selector',
                        'HTTP method',
                        'Git command',
                    ],
                    'A',
                ],
                'sql-analytics' => [
                    'Ranking dalam kelompok kategori dapat dibantu oleh?',
                    [
                        'Window function',
                        'HTML form',
                        'Cookie',
                        'Flexbox',
                    ],
                    'A',
                ],
                'python-data' => [
                    'List comprehension digunakan untuk?',
                    [
                        'Membentuk koleksi baru secara ringkas',
                        'Membuat index database',
                        'Mengatur DNS',
                        'Mengirim mail',
                    ],
                    'A',
                ],
                'pandas' => [
                    'DataFrame adalah struktur data dua dimensi yang umum pada?',
                    ['Pandas', 'CSS', 'Git', 'Nginx'],
                    'A',
                ],
                'data-visualization' => [
                    'Untuk membandingkan nilai antar kategori, pilihan awal yang masuk akal?',
                    [
                        'Bar chart',
                        'Pie 50 kategori',
                        'Teks acak',
                        'Captcha',
                    ],
                    'A',
                ],
                'git-github' => [
                    'Mengapa analis data tetap berguna memahami Git?',
                    [
                        'Melacak perubahan script dan analisis',
                        'Menggantikan statistik',
                        'Membuat spreadsheet otomatis tanpa code',
                        'Menghapus kebutuhan data',
                    ],
                    'A',
                ],
            ],
        ];

        $skills = Skill::query()
            ->get()
            ->keyBy('slug');

        foreach ($sets as $careerSlug => $questions) {
            $career = Career::query()
                ->where('slug', $careerSlug)
                ->firstOrFail();

            $assessment = Assessment::updateOrCreate(
                ['career_id' => $career->id],
                [
                    'title' => 'Asesmen Awal '.$career->name,
                    'description' => 'Jawab berdasarkan kemampuan saat ini. Skor objektif digabung dengan penilaian diri agar sistem memiliki titik awal yang lebih masuk akal.',
                    'duration_minutes' => 20,
                    'is_active' => true,
                ],
            );

            $assessment->questions()->delete();

            foreach ($questions as $skillSlug => [$prompt, $options, $answer]) {
                $skill = $skills->get($skillSlug);

                if (! $skill) {
                    continue;
                }

                AssessmentQuestion::create([
                    'assessment_id' => $assessment->id,
                    'skill_id' => $skill->id,
                    'prompt' => $prompt,
                    'options' => [
                        'A' => $options[0],
                        'B' => $options[1],
                        'C' => $options[2],
                        'D' => $options[3],
                    ],
                    'correct_answer' => $answer,
                    'explanation' => 'Gunakan konsep dasar skill ini untuk menentukan jawaban, bukan sekadar menghafal istilah.',
                    'difficulty' => 'Dasar',
                ]);
            }
        }
    }
}
