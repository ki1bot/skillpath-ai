<?php

namespace Database\Seeders;

use App\Models\LearningMaterial;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LearningMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            'programming-fundamentals' => [
                'Fondasi Pemrograman yang Benar',
                'Latih cara berpikir program sebelum mengejar framework.',
                'Buat program CLI kecil untuk mencatat tiga tugas dan statusnya.',
                'Apa tujuan utama function dalam program?',
                [
                    'Menyimpan database',
                    'Mengelompokkan logika yang dapat dipakai ulang',
                    'Mengganti sistem operasi',
                    'Membuat koneksi internet',
                ],
                'B',
            ],
            'git-github' => [
                'Workflow Git untuk Proyek Nyata',
                'Pelajari commit yang rapi, branch, merge, dan remote tanpa menghafal perintah secara buta.',
                'Buat repository latihan, lakukan tiga commit bermakna, lalu merge satu feature branch.',
                'Perintah apa yang membuat snapshot perubahan ke riwayat lokal Git?',
                ['git push', 'git commit', 'git clone', 'git status'],
                'B',
            ],
            'terminal-cli' => [
                'Terminal Tanpa Tebak-tebakan',
                'Bangun kebiasaan membaca path, output, exit code, dan environment sebelum menjalankan perintah.',
                'Navigasikan folder proyek, buat file, pindahkan file, lalu tampilkan environment variable.',
                'Perintah pwd digunakan untuk apa?',
                [
                    'Melihat direktori aktif',
                    'Menghapus file',
                    'Menginstal paket',
                    'Membuat database',
                ],
                'A',
            ],
            'http-fundamentals' => [
                'Cara Web Berkomunikasi dengan HTTP',
                'Pahami apa yang sebenarnya terjadi ketika browser atau client memanggil server.',
                'Amati satu request API dan catat method, URL, status code, header, dan body.',
                'Status HTTP 404 paling tepat berarti apa?',
                [
                    'Server berhasil memproses data',
                    'Resource tidak ditemukan',
                    'User sudah login',
                    'Database terkunci',
                ],
                'B',
            ],
            'database-fundamentals' => [
                'Merancang Data Relasional',
                'Belajar menyusun tabel dari kebutuhan, bukan dari tampilan form.',
                'Rancang tabel user, project, dan task lengkap dengan primary key serta foreign key.',
                'Apa fungsi foreign key?',
                [
                    'Membatasi panjang teks',
                    'Menghubungkan dan menjaga referensi antar tabel',
                    'Mengubah warna tabel',
                    'Menghapus semua duplikasi otomatis',
                ],
                'B',
            ],
            'sql' => [
                'SQL untuk Membaca dan Mengubah Data',
                'Fokus pada query yang dapat dijelaskan: filter, join, agregasi, dan transaksi.',
                'Tulis query laporan jumlah task per user menggunakan JOIN dan GROUP BY.',
                'Klausa SQL apa yang digunakan untuk mengelompokkan baris sebelum agregasi?',
                ['ORDER BY', 'GROUP BY', 'LIMIT', 'VALUES'],
                'B',
            ],
            'testing-fundamentals' => [
                'Testing yang Menjaga Perilaku',
                'Uji perilaku penting dan edge case, bukan sekadar mengejar angka coverage.',
                'Tulis test untuk satu alur sukses dan dua alur gagal pada fitur login atau API.',
                'Apa yang sebaiknya diuji feature test?',
                [
                    'Warna editor',
                    'Perilaku fitur dari input sampai respons',
                    'Nama branch Git',
                    'Versi browser pengguna',
                ],
                'B',
            ],
            'deployment-basics' => [
                'Dari Localhost ke Production',
                'Kenali perbedaan build, environment, database, queue, HTTPS, dan observability saat aplikasi dirilis.',
                'Buat checklist deployment untuk project Anda dan tandai secret yang tidak boleh masuk repository.',
                'Di mana API key production sebaiknya disimpan?',
                [
                    'Di source code',
                    'Di README publik',
                    'Di environment/secret manager',
                    'Di nama branch',
                ],
                'C',
            ],
            'php-laravel' => [
                'Laravel sebagai Sistem, Bukan Kumpulan Magic',
                'Pahami alur request ke route, controller, service, validation, model, lalu response.',
                'Buat endpoint sederhana yang menerima input tervalidasi dan menyimpan satu resource.',
                'Komponen Laravel mana yang lazim dipakai untuk memvalidasi input request?',
                [
                    'Validator atau Form Request',
                    'Migration saja',
                    'Seeder saja',
                    'Blade directive',
                ],
                'A',
            ],
            'rest-api' => [
                'Merancang REST API yang Konsisten',
                'Susun endpoint berdasarkan resource dan gunakan status code yang sesuai dengan hasil operasi.',
                'Rancang endpoint CRUD task lengkap dengan contoh request dan response error.',
                'Method HTTP paling tepat untuk membuat resource baru adalah?',
                ['GET', 'POST', 'DELETE', 'HEAD'],
                'B',
            ],
            'authentication-authorization' => [
                'Identitas, Role, dan Batas Akses',
                'Bedakan proses membuktikan identitas dengan menentukan apa yang boleh dilakukan user.',
                'Buat matriks akses student dan admin untuk lima aksi di aplikasi.',
                'Authorization menjawab pertanyaan apa?',
                [
                    'Siapa pengguna ini?',
                    'Apa yang boleh dilakukan pengguna ini?',
                    'Berapa ukuran database?',
                    'Di mana server berada?',
                ],
                'B',
            ],
            'eloquent-orm' => [
                'Relasi Eloquent Tanpa Query Boros',
                'Gunakan relationship dan eager loading sambil tetap memahami SQL yang dihasilkan.',
                'Modelkan relasi Career, Skill, dan CareerSkill lalu tampilkan data tanpa N+1 query.',
                'Teknik apa yang digunakan untuk mengurangi N+1 query pada relasi Eloquent?',
                [
                    'Eager loading',
                    'Hard coding',
                    'Refresh browser',
                    'Menambah CSS',
                ],
                'A',
            ],
            'validation-error-handling' => [
                'Input Salah Harus Gagal dengan Jelas',
                'Buat aturan validasi dan respons error yang membantu client memperbaiki input.',
                'Tambahkan validasi untuk endpoint create task dan uji minimal tiga input tidak valid.',
                'Status HTTP umum untuk validation error di Laravel adalah?',
                ['201', '204', '422', '301'],
                'C',
            ],
            'logging-monitoring' => [
                'Mencari Masalah dari Bukti',
                'Belajar menulis log yang berguna dan membedakan informasi, peringatan, serta error.',
                'Tambahkan log pada satu proses penting tanpa menyimpan password atau token.',
                'Data mana yang tidak boleh ditulis ke log?',
                [
                    'ID request',
                    'Nama proses',
                    'Password pengguna',
                    'Durasi proses',
                ],
                'C',
            ],
            'web-security-basics' => [
                'Keamanan Web Dasar untuk Developer',
                'Kenali serangan umum dan bangun kebiasaan validasi, escaping, hashing, serta least privilege.',
                'Audit satu form dan satu endpoint dari risiko injection, XSS, CSRF, dan akses tanpa izin.',
                'Password pengguna seharusnya disimpan sebagai?',
                [
                    'Plain text',
                    'Hash yang aman',
                    'Nama file',
                    'Cookie publik',
                ],
                'B',
            ],
            'html-semantics' => [
                'HTML yang Memiliki Arti',
                'Gunakan struktur dokumen yang membantu browser, keyboard user, dan screen reader memahami halaman.',
                'Ubah halaman div-only menjadi struktur header, nav, main, section, dan footer yang tepat.',
                'Elemen mana yang paling tepat untuk navigasi utama?',
                ['nav', 'span', 'b', 'canvas'],
                'A',
            ],
            'css-responsive' => [
                'Layout Responsif yang Tidak Rapuh',
                'Bangun layout dari content flow lalu gunakan flex, grid, dan breakpoint saat memang diperlukan.',
                'Buat kartu tiga kolom di desktop yang berubah menjadi satu kolom di mobile.',
                'CSS Grid paling cocok ketika kebutuhan utama adalah?',
                [
                    'Layout dua dimensi baris dan kolom',
                    'Menyimpan password',
                    'Menulis SQL',
                    'Mengirim email',
                ],
                'A',
            ],
            'javascript' => [
                'JavaScript untuk Logika UI',
                'Kuasai data, function, async flow, module, dan transformasi array sebelum masuk framework.',
                'Ambil array task lalu filter task selesai dan hitung totalnya tanpa mutasi data asli.',
                'Promise digunakan untuk merepresentasikan apa?',
                [
                    'Nilai dari operasi asynchronous',
                    'Warna CSS',
                    'Tabel database',
                    'Branch Git',
                ],
                'A',
            ],
            'typescript' => [
                'TypeScript yang Membantu, Bukan Menghambat',
                'Modelkan bentuk data penting dan gunakan narrowing agar error muncul sebelum runtime.',
                'Buat type User, Career, dan union status roadmap lalu gunakan pada function sederhana.',
                'Apa manfaat utama union type?',
                [
                    'Membatasi nilai ke beberapa kemungkinan yang jelas',
                    'Menghapus database',
                    'Menambah latency',
                    'Mengganti HTML',
                ],
                'A',
            ],
            'react' => [
                'React dari Component sampai Data Flow',
                'Bangun komponen kecil dengan props dan state yang alurnya dapat diikuti.',
                'Buat daftar skill dengan filter status dan komponen progress yang reusable.',
                'State React sebaiknya digunakan untuk data yang?',
                [
                    'Dapat berubah dan memengaruhi render',
                    'Selalu konstan di seluruh internet',
                    'Hanya ada di database server',
                    'Tidak pernah dibaca UI',
                ],
                'A',
            ],
            'state-management' => [
                'Menaruh State di Tempat yang Tepat',
                'Hindari global state untuk semua hal; pilih lokasi state berdasarkan siapa yang membutuhkan data.',
                'Petakan state halaman dashboard dan tentukan mana yang lokal, shared, atau server state.',
                'Kapan state lokal lebih tepat?',
                [
                    'Saat hanya satu area komponen yang membutuhkannya',
                    'Saat semua aplikasi di internet membutuhkannya',
                    'Saat ingin mengganti database',
                    'Saat menghapus route',
                ],
                'A',
            ],
            'accessibility' => [
                'Aksesibilitas sebagai Bagian dari UI',
                'Pastikan keyboard flow, label, focus, semantic, dan kontras tidak menjadi pekerjaan terakhir.',
                'Navigasikan halaman hanya dengan keyboard lalu perbaiki elemen yang tidak dapat difokuskan dengan benar.',
                'Input form yang baik perlu memiliki apa?',
                [
                    'Label yang terhubung',
                    'Placeholder saja',
                    'Warna acak',
                    'Animasi terus-menerus',
                ],
                'A',
            ],
            'web-performance' => [
                'Performa yang Terasa oleh Pengguna',
                'Cari biaya terbesar pada network, image, JavaScript, dan render sebelum melakukan optimasi mikro.',
                'Audit satu halaman dan catat tiga asset atau proses yang paling mahal.',
                'Lazy loading paling berguna untuk?',
                [
                    'Menunda resource yang belum dibutuhkan',
                    'Menghapus autentikasi',
                    'Mengganti SQL',
                    'Membuat password',
                ],
                'A',
            ],
            'spreadsheet-analysis' => [
                'Spreadsheet untuk Analisis Cepat',
                'Gunakan formula, pivot, filter, dan validasi dengan struktur yang tetap dapat diaudit.',
                'Buat ringkasan penjualan per kategori dari dataset kecil menggunakan pivot table.',
                'Pivot table terutama digunakan untuk?',
                [
                    'Merangkum data berdasarkan dimensi',
                    'Menulis backend API',
                    'Membuat CSS',
                    'Mengelola branch',
                ],
                'A',
            ],
            'statistics-fundamentals' => [
                'Statistik untuk Membaca Data dengan Waras',
                'Pelajari mean, median, sebaran, korelasi, dan sampling tanpa mengubah angka menjadi kepastian palsu.',
                'Hitung mean, median, standar deviasi, lalu jelaskan kapan median lebih berguna.',
                'Median lebih tahan terhadap apa dibanding mean?',
                [
                    'Outlier ekstrem',
                    'Nama kolom',
                    'Warna chart',
                    'Ukuran file',
                ],
                'A',
            ],
            'data-cleaning' => [
                'Membersihkan Data Sebelum Percaya Hasil',
                'Periksa tipe data, missing value, duplikasi, format, dan outlier sebelum analisis.',
                'Buat checklist cleaning untuk dataset transaksi dan dokumentasikan setiap perubahan.',
                'Langkah awal yang masuk akal saat menemukan missing value adalah?',
                [
                    'Memahami pola dan konteksnya',
                    'Selalu mengisi nol',
                    'Menghapus seluruh dataset',
                    'Mengubah semua menjadi teks',
                ],
                'A',
            ],
            'python-data' => [
                'Python untuk Alur Analisis',
                'Gunakan Python untuk membaca data, transformasi, fungsi kecil, dan otomasi pekerjaan berulang.',
                'Baca file CSV, validasi kolom wajib, lalu hitung ringkasan sederhana.',
                'Library standar Python untuk membaca file CSV adalah?',
                ['csv', 'css', 'httpd-only', 'git'],
                'A',
            ],
            'pandas' => [
                'Pandas untuk DataFrame',
                'Kuasai filter, groupby, merge, missing value, dan reshape dengan perubahan yang mudah dilacak.',
                'Gabungkan dua DataFrame berdasarkan customer_id lalu hitung total transaksi per customer.',
                'Method Pandas yang umum untuk menggabungkan DataFrame berdasarkan key adalah?',
                ['merge', 'paint', 'route', 'deploy'],
                'A',
            ],
            'data-visualization' => [
                'Visualisasi yang Menjawab Pertanyaan',
                'Pilih chart berdasarkan hubungan yang ingin ditunjukkan, bukan karena bentuknya menarik.',
                'Buat tiga chart dari satu dataset dan tulis satu kalimat insight untuk masing-masing.',
                'Chart apa yang umum dipakai untuk membandingkan kategori?',
                [
                    'Bar chart',
                    'Scatter plot waktu tanpa sumbu',
                    'Captcha',
                    'Terminal tree',
                ],
                'A',
            ],
            'sql-analytics' => [
                'SQL untuk Analisis yang Lebih Dalam',
                'Gunakan CTE dan window function untuk ranking, running total, serta perbandingan antar periode.',
                'Tulis query ranking produk per kategori berdasarkan total penjualan.',
                'Window function memungkinkan perhitungan tanpa melakukan apa pada setiap baris hasil?',
                [
                    'Menggabungkan seluruh hasil menjadi satu baris',
                    'Menghapus tabel',
                    'Mengubah CSS',
                    'Membuat token',
                ],
                'A',
            ],
        ];

        $skills = Skill::query()
            ->get()
            ->keyBy('slug');

        foreach ($materials as $slug => [$title, $summary, $practice, $quiz, $options, $answer]) {
            $skill = $skills->get($slug);

            if (! $skill) {
                continue;
            }

            LearningMaterial::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'skill_id' => $skill->id,
                    'title' => $title,
                    'summary' => $summary,
                    'learning_objectives' => [
                        'Memahami konsep inti '.$skill->name,
                        'Menerapkan konsep pada latihan kecil yang dapat diperiksa',
                        'Menjelaskan alasan di balik solusi yang dipilih',
                    ],
                    'difficulty' => $skill->difficulty,
                    'estimated_minutes' => $skill->difficulty === 'Dasar'
                        ? 90
                        : 120,
                    'resource_title' => 'Referensi pilihan SkillPath',
                    'resource_url' => null,
                    'practice_task' => $practice,
                    'quiz_question' => $quiz,
                    'quiz_options' => [
                        'A' => $options[0],
                        'B' => $options[1],
                        'C' => $options[2],
                        'D' => $options[3],
                    ],
                    'quiz_answer' => $answer,
                    'quiz_explanation' => 'Periksa kembali konsep inti pada materi dan hubungkan jawaban dengan konteks soal.',
                ],
            );
        }
    }
}
