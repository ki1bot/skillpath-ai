<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResult;
use App\Models\Career;
use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserSkill;
use App\Services\CareerReadinessService;
use App\Services\RoadmapService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $careers = $this->seedCareers();
        $skills = $this->seedSkills();
        $this->seedCareerSkills($careers, $skills);
        $this->seedPrerequisites($skills);
        $this->seedMaterials($skills);
        $assessments = $this->seedAssessments($careers, $skills);
        $this->seedProjects($careers, $skills);
        $this->seedUsers($careers, $skills, $assessments);
    }

    private function seedCareers(): array
    {
        $rows = [
            'backend' => [
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
            ],
            'frontend' => [
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
            ],
            'data' => [
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
            ],
        ];

        $result = [];

        foreach ($rows as $key => $row) {
            $result[$key] = Career::updateOrCreate(
                ['slug' => $row['slug']],
                [...$row, 'is_active' => true],
            );
        }

        return $result;
    }

    private function seedSkills(): array
    {
        $rows = [
            ['programming-fundamentals', 'Dasar Pemrograman', 'Fondasi', 'Memahami variabel, kondisi, perulangan, fungsi, struktur data sederhana, dan cara memecah masalah menjadi langkah program.', 'Dasar'],
            ['git-github', 'Git & GitHub', 'Tools', 'Mengelola perubahan kode dengan commit, branch, merge, remote, dan workflow kolaborasi dasar.', 'Dasar'],
            ['terminal-cli', 'Terminal & CLI', 'Tools', 'Bekerja dengan filesystem, proses, environment, dan command line secara aman dan efisien.', 'Dasar'],
            ['http-fundamentals', 'HTTP Fundamentals', 'Web', 'Memahami request-response, method, status code, header, cookie, dan konsep client-server.', 'Dasar'],
            ['database-fundamentals', 'Database Fundamentals', 'Data', 'Memahami tabel, relasi, primary key, foreign key, constraint, indeks, dan prinsip desain data relasional.', 'Dasar'],
            ['sql', 'SQL', 'Data', 'Menulis query SELECT, JOIN, agregasi, subquery, insert, update, delete, dan transaksi dasar.', 'Dasar'],
            ['testing-fundamentals', 'Testing Fundamentals', 'Quality', 'Membedakan unit, integration, dan feature test serta menulis pengujian untuk perilaku penting aplikasi.', 'Menengah'],
            ['deployment-basics', 'Deployment Basics', 'Delivery', 'Memahami environment variable, build, server, database production, HTTPS, dan proses rilis aplikasi.', 'Menengah'],
            ['php-laravel', 'PHP & Laravel', 'Backend', 'Menggunakan PHP modern dan Laravel untuk routing, controller, service, validation, model, dan dependency injection.', 'Menengah'],
            ['rest-api', 'REST API', 'Backend', 'Merancang endpoint, resource, status code, pagination, filtering, dan kontrak API yang konsisten.', 'Menengah'],
            ['authentication-authorization', 'Authentication & Authorization', 'Backend', 'Menerapkan identitas pengguna, session atau token, role, permission, dan pembatasan akses resource.', 'Menengah'],
            ['eloquent-orm', 'Eloquent ORM', 'Backend', 'Memodelkan relasi data dan melakukan query dengan Eloquent secara efisien tanpa kehilangan pemahaman SQL.', 'Menengah'],
            ['validation-error-handling', 'Validation & Error Handling', 'Backend', 'Memvalidasi input, menata error response, dan menangani kondisi gagal secara konsisten.', 'Menengah'],
            ['logging-monitoring', 'Logging & Monitoring', 'Backend', 'Mencatat kejadian penting, membedakan level log, dan menemukan masalah runtime dari bukti yang tersedia.', 'Menengah'],
            ['web-security-basics', 'Web Security Basics', 'Security', 'Memahami CSRF, XSS, injection, hashing, rate limiting, dan prinsip least privilege.', 'Menengah'],
            ['html-semantics', 'HTML Semantics', 'Frontend', 'Menyusun struktur dokumen dengan elemen semantik yang tepat untuk aksesibilitas dan maintainability.', 'Dasar'],
            ['css-responsive', 'CSS & Responsive Design', 'Frontend', 'Membangun layout adaptif dengan box model, flexbox, grid, breakpoint, dan pola responsif.', 'Dasar'],
            ['javascript', 'JavaScript', 'Frontend', 'Menguasai tipe data, function, object, array, async flow, module, dan manipulasi data di browser.', 'Dasar'],
            ['typescript', 'TypeScript', 'Frontend', 'Memodelkan data dengan type, interface, union, generic, narrowing, dan inference untuk mengurangi bug.', 'Menengah'],
            ['react', 'React', 'Frontend', 'Membangun UI berbasis component, props, state, event, effect, form, dan composition.', 'Menengah'],
            ['state-management', 'State Management', 'Frontend', 'Memilih state lokal, server state, context, dan pola pengelolaan state tanpa kompleksitas berlebihan.', 'Menengah'],
            ['accessibility', 'Web Accessibility', 'Frontend', 'Membuat UI yang dapat dipakai keyboard dan assistive technology dengan semantic, focus, label, dan contrast yang tepat.', 'Menengah'],
            ['web-performance', 'Web Performance', 'Frontend', 'Mengurangi biaya render, asset, network, dan JavaScript untuk memperbaiki pengalaman pengguna.', 'Menengah'],
            ['spreadsheet-analysis', 'Spreadsheet Analysis', 'Data Analyst', 'Mengolah data dengan formula, lookup, pivot, filter, dan validasi pada spreadsheet.', 'Dasar'],
            ['statistics-fundamentals', 'Statistics Fundamentals', 'Data Analyst', 'Memahami ukuran pemusatan, sebaran, distribusi, korelasi, sampling, dan interpretasi sederhana.', 'Dasar'],
            ['data-cleaning', 'Data Cleaning', 'Data Analyst', 'Menangani missing value, duplikasi, tipe data, outlier, dan inkonsistensi sebelum analisis.', 'Menengah'],
            ['python-data', 'Python for Data Analysis', 'Data Analyst', 'Menggunakan Python untuk transformasi data, otomasi analisis, dan eksplorasi dataset.', 'Menengah'],
            ['pandas', 'Pandas', 'Data Analyst', 'Menggunakan DataFrame untuk filtering, grouping, merge, reshape, cleaning, dan agregasi data.', 'Menengah'],
            ['data-visualization', 'Data Visualization', 'Data Analyst', 'Memilih visual yang tepat dan menyampaikan pola data tanpa menyesatkan pembaca.', 'Menengah'],
            ['sql-analytics', 'SQL Analytics', 'Data Analyst', 'Menulis query analitik dengan CTE, window function, cohort sederhana, dan metrik berbasis waktu.', 'Menengah'],
        ];

        $result = [];

        foreach ($rows as [$slug, $name, $category, $description, $difficulty]) {
            $result[$slug] = Skill::updateOrCreate(
                ['slug' => $slug],
                compact(
                    'name',
                    'category',
                    'description',
                    'difficulty',
                ),
            );
        }

        return $result;
    }

    private function seedCareerSkills(
        array $careers,
        array $skills,
    ): void {
        $maps = [
            'backend' => [
                'programming-fundamentals' => [75, 1.50],
                'git-github' => [65, 1.00],
                'terminal-cli' => [60, 0.85],
                'http-fundamentals' => [80, 1.35],
                'database-fundamentals' => [75, 1.35],
                'sql' => [75, 1.25],
                'php-laravel' => [80, 1.45],
                'rest-api' => [85, 1.50],
                'authentication-authorization' => [75, 1.35],
                'eloquent-orm' => [75, 1.15],
                'validation-error-handling' => [75, 1.15],
                'testing-fundamentals' => [65, 1.00],
                'logging-monitoring' => [60, 0.90],
                'deployment-basics' => [60, 0.90],
                'web-security-basics' => [65, 1.10],
            ],
            'frontend' => [
                'git-github' => [60, 0.90],
                'http-fundamentals' => [70, 1.00],
                'html-semantics' => [80, 1.35],
                'css-responsive' => [80, 1.35],
                'javascript' => [85, 1.50],
                'typescript' => [75, 1.20],
                'react' => [85, 1.50],
                'state-management' => [70, 1.05],
                'accessibility' => [70, 1.10],
                'testing-fundamentals' => [60, 0.90],
                'web-performance' => [65, 0.95],
                'deployment-basics' => [55, 0.80],
            ],
            'data' => [
                'spreadsheet-analysis' => [75, 1.10],
                'statistics-fundamentals' => [75, 1.35],
                'data-cleaning' => [80, 1.45],
                'database-fundamentals' => [60, 0.90],
                'sql' => [80, 1.45],
                'sql-analytics' => [75, 1.30],
                'python-data' => [70, 1.05],
                'pandas' => [75, 1.20],
                'data-visualization' => [80, 1.35],
                'git-github' => [50, 0.70],
            ],
        ];

        foreach ($maps as $careerKey => $mapping) {
            $sync = [];

            foreach ($mapping as $slug => [$target, $weight]) {
                $sync[$skills[$slug]->id] = [
                    'target_level' => $target,
                    'importance_weight' => $weight,
                    'is_required' => $weight >= 0.90,
                ];
            }

            $careers[$careerKey]->skills()->sync($sync);
        }
    }

    private function seedPrerequisites(array $skills): void
    {
        $pairs = [
            ['sql', 'database-fundamentals'],
            ['php-laravel', 'programming-fundamentals'],
            ['rest-api', 'http-fundamentals'],
            ['rest-api', 'php-laravel'],
            ['rest-api', 'eloquent-orm'],
            ['authentication-authorization', 'rest-api'],
            ['eloquent-orm', 'database-fundamentals'],
            ['eloquent-orm', 'php-laravel'],
            ['validation-error-handling', 'rest-api'],
            ['logging-monitoring', 'rest-api'],
            ['web-security-basics', 'authentication-authorization'],
            ['deployment-basics', 'git-github'],
            ['deployment-basics', 'terminal-cli'],
            ['css-responsive', 'html-semantics'],
            ['javascript', 'html-semantics'],
            ['typescript', 'javascript'],
            ['react', 'javascript'],
            ['react', 'typescript'],
            ['state-management', 'react'],
            ['accessibility', 'html-semantics'],
            ['accessibility', 'css-responsive'],
            ['web-performance', 'react'],
            ['python-data', 'programming-fundamentals'],
            ['pandas', 'python-data'],
            ['data-cleaning', 'spreadsheet-analysis'],
            ['data-visualization', 'data-cleaning'],
            ['sql-analytics', 'sql'],
            ['sql-analytics', 'statistics-fundamentals'],
        ];

        DB::table('skill_prerequisites')->delete();

        foreach ($pairs as [$skill, $prerequisite]) {
            DB::table('skill_prerequisites')->insert([
                'skill_id' => $skills[$skill]->id,
                'prerequisite_skill_id' => $skills[$prerequisite]->id,
                'factor' => 1.20,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedMaterials(array $skills): void
    {
        $materials = [
            'programming-fundamentals' => ['Fondasi Pemrograman yang Benar', 'Latih cara berpikir program sebelum mengejar framework.', 'Buat program CLI kecil untuk mencatat tiga tugas dan statusnya.', 'Apa tujuan utama function dalam program?', ['Menyimpan database', 'Mengelompokkan logika yang dapat dipakai ulang', 'Mengganti sistem operasi', 'Membuat koneksi internet'], 'B'],
            'git-github' => ['Workflow Git untuk Proyek Nyata', 'Pelajari commit yang rapi, branch, merge, dan remote tanpa menghafal perintah secara buta.', 'Buat repository latihan, lakukan tiga commit bermakna, lalu merge satu feature branch.', 'Perintah apa yang membuat snapshot perubahan ke riwayat lokal Git?', ['git push', 'git commit', 'git clone', 'git status'], 'B'],
            'terminal-cli' => ['Terminal Tanpa Tebak-tebakan', 'Bangun kebiasaan membaca path, output, exit code, dan environment sebelum menjalankan perintah.', 'Navigasikan folder proyek, buat file, pindahkan file, lalu tampilkan environment variable.', 'Perintah pwd digunakan untuk apa?', ['Melihat direktori aktif', 'Menghapus file', 'Menginstal paket', 'Membuat database'], 'A'],
            'http-fundamentals' => ['Cara Web Berkomunikasi dengan HTTP', 'Pahami apa yang sebenarnya terjadi ketika browser atau client memanggil server.', 'Amati satu request API dan catat method, URL, status code, header, dan body.', 'Status HTTP 404 paling tepat berarti apa?', ['Server berhasil memproses data', 'Resource tidak ditemukan', 'User sudah login', 'Database terkunci'], 'B'],
            'database-fundamentals' => ['Merancang Data Relasional', 'Belajar menyusun tabel dari kebutuhan, bukan dari tampilan form.', 'Rancang tabel user, project, dan task lengkap dengan primary key serta foreign key.', 'Apa fungsi foreign key?', ['Membatasi panjang teks', 'Menghubungkan dan menjaga referensi antar tabel', 'Mengubah warna tabel', 'Menghapus semua duplikasi otomatis'], 'B'],
            'sql' => ['SQL untuk Membaca dan Mengubah Data', 'Fokus pada query yang dapat dijelaskan: filter, join, agregasi, dan transaksi.', 'Tulis query laporan jumlah task per user menggunakan JOIN dan GROUP BY.', 'Klausa SQL apa yang digunakan untuk mengelompokkan baris sebelum agregasi?', ['ORDER BY', 'GROUP BY', 'LIMIT', 'VALUES'], 'B'],
            'testing-fundamentals' => ['Testing yang Menjaga Perilaku', 'Uji perilaku penting dan edge case, bukan sekadar mengejar angka coverage.', 'Tulis test untuk satu alur sukses dan dua alur gagal pada fitur login atau API.', 'Apa yang sebaiknya diuji feature test?', ['Warna editor', 'Perilaku fitur dari input sampai respons', 'Nama branch Git', 'Versi browser pengguna'], 'B'],
            'deployment-basics' => ['Dari Localhost ke Production', 'Kenali perbedaan build, environment, database, queue, HTTPS, dan observability saat aplikasi dirilis.', 'Buat checklist deployment untuk project Anda dan tandai secret yang tidak boleh masuk repository.', 'Di mana API key production sebaiknya disimpan?', ['Di source code', 'Di README publik', 'Di environment/secret manager', 'Di nama branch'], 'C'],
            'php-laravel' => ['Laravel sebagai Sistem, Bukan Kumpulan Magic', 'Pahami alur request ke route, controller, service, validation, model, lalu response.', 'Buat endpoint sederhana yang menerima input tervalidasi dan menyimpan satu resource.', 'Komponen Laravel mana yang lazim dipakai untuk memvalidasi input request?', ['Validator atau Form Request', 'Migration saja', 'Seeder saja', 'Blade directive'], 'A'],
            'rest-api' => ['Merancang REST API yang Konsisten', 'Susun endpoint berdasarkan resource dan gunakan status code yang sesuai dengan hasil operasi.', 'Rancang endpoint CRUD task lengkap dengan contoh request dan response error.', 'Method HTTP paling tepat untuk membuat resource baru adalah?', ['GET', 'POST', 'DELETE', 'HEAD'], 'B'],
            'authentication-authorization' => ['Identitas, Role, dan Batas Akses', 'Bedakan proses membuktikan identitas dengan menentukan apa yang boleh dilakukan user.', 'Buat matriks akses student dan admin untuk lima aksi di aplikasi.', 'Authorization menjawab pertanyaan apa?', ['Siapa pengguna ini?', 'Apa yang boleh dilakukan pengguna ini?', 'Berapa ukuran database?', 'Di mana server berada?'], 'B'],
            'eloquent-orm' => ['Relasi Eloquent Tanpa Query Boros', 'Gunakan relationship dan eager loading sambil tetap memahami SQL yang dihasilkan.', 'Modelkan relasi Career, Skill, dan CareerSkill lalu tampilkan data tanpa N+1 query.', 'Teknik apa yang digunakan untuk mengurangi N+1 query pada relasi Eloquent?', ['Eager loading', 'Hard coding', 'Refresh browser', 'Menambah CSS'], 'A'],
            'validation-error-handling' => ['Input Salah Harus Gagal dengan Jelas', 'Buat aturan validasi dan respons error yang membantu client memperbaiki input.', 'Tambahkan validasi untuk endpoint create task dan uji minimal tiga input tidak valid.', 'Status HTTP umum untuk validation error di Laravel adalah?', ['201', '204', '422', '301'], 'C'],
            'logging-monitoring' => ['Mencari Masalah dari Bukti', 'Belajar menulis log yang berguna dan membedakan informasi, peringatan, serta error.', 'Tambahkan log pada satu proses penting tanpa menyimpan password atau token.', 'Data mana yang tidak boleh ditulis ke log?', ['ID request', 'Nama proses', 'Password pengguna', 'Durasi proses'], 'C'],
            'web-security-basics' => ['Keamanan Web Dasar untuk Developer', 'Kenali serangan umum dan bangun kebiasaan validasi, escaping, hashing, serta least privilege.', 'Audit satu form dan satu endpoint dari risiko injection, XSS, CSRF, dan akses tanpa izin.', 'Password pengguna seharusnya disimpan sebagai?', ['Plain text', 'Hash yang aman', 'Nama file', 'Cookie publik'], 'B'],
            'html-semantics' => ['HTML yang Memiliki Arti', 'Gunakan struktur dokumen yang membantu browser, keyboard user, dan screen reader memahami halaman.', 'Ubah halaman div-only menjadi struktur header, nav, main, section, dan footer yang tepat.', 'Elemen mana yang paling tepat untuk navigasi utama?', ['nav', 'span', 'b', 'canvas'], 'A'],
            'css-responsive' => ['Layout Responsif yang Tidak Rapuh', 'Bangun layout dari content flow lalu gunakan flex, grid, dan breakpoint saat memang diperlukan.', 'Buat kartu tiga kolom di desktop yang berubah menjadi satu kolom di mobile.', 'CSS Grid paling cocok ketika kebutuhan utama adalah?', ['Layout dua dimensi baris dan kolom', 'Menyimpan password', 'Menulis SQL', 'Mengirim email'], 'A'],
            'javascript' => ['JavaScript untuk Logika UI', 'Kuasai data, function, async flow, module, dan transformasi array sebelum masuk framework.', 'Ambil array task lalu filter task selesai dan hitung totalnya tanpa mutasi data asli.', 'Promise digunakan untuk merepresentasikan apa?', ['Nilai dari operasi asynchronous', 'Warna CSS', 'Tabel database', 'Branch Git'], 'A'],
            'typescript' => ['TypeScript yang Membantu, Bukan Menghambat', 'Modelkan bentuk data penting dan gunakan narrowing agar error muncul sebelum runtime.', 'Buat type User, Career, dan union status roadmap lalu gunakan pada function sederhana.', 'Apa manfaat utama union type?', ['Membatasi nilai ke beberapa kemungkinan yang jelas', 'Menghapus database', 'Menambah latency', 'Mengganti HTML'], 'A'],
            'react' => ['React dari Component sampai Data Flow', 'Bangun komponen kecil dengan props dan state yang alurnya dapat diikuti.', 'Buat daftar skill dengan filter status dan komponen progress yang reusable.', 'State React sebaiknya digunakan untuk data yang?', ['Dapat berubah dan memengaruhi render', 'Selalu konstan di seluruh internet', 'Hanya ada di database server', 'Tidak pernah dibaca UI'], 'A'],
            'state-management' => ['Menaruh State di Tempat yang Tepat', 'Hindari global state untuk semua hal; pilih lokasi state berdasarkan siapa yang membutuhkan data.', 'Petakan state halaman dashboard dan tentukan mana yang lokal, shared, atau server state.', 'Kapan state lokal lebih tepat?', ['Saat hanya satu area komponen yang membutuhkannya', 'Saat semua aplikasi di internet membutuhkannya', 'Saat ingin mengganti database', 'Saat menghapus route'], 'A'],
            'accessibility' => ['Aksesibilitas sebagai Bagian dari UI', 'Pastikan keyboard flow, label, focus, semantic, dan kontras tidak menjadi pekerjaan terakhir.', 'Navigasikan halaman hanya dengan keyboard lalu perbaiki elemen yang tidak dapat difokuskan dengan benar.', 'Input form yang baik perlu memiliki apa?', ['Label yang terhubung', 'Placeholder saja', 'Warna acak', 'Animasi terus-menerus'], 'A'],
            'web-performance' => ['Performa yang Terasa oleh Pengguna', 'Cari biaya terbesar pada network, image, JavaScript, dan render sebelum melakukan optimasi mikro.', 'Audit satu halaman dan catat tiga asset atau proses yang paling mahal.', 'Lazy loading paling berguna untuk?', ['Menunda resource yang belum dibutuhkan', 'Menghapus autentikasi', 'Mengganti SQL', 'Membuat password'], 'A'],
            'spreadsheet-analysis' => ['Spreadsheet untuk Analisis Cepat', 'Gunakan formula, pivot, filter, dan validasi dengan struktur yang tetap dapat diaudit.', 'Buat ringkasan penjualan per kategori dari dataset kecil menggunakan pivot table.', 'Pivot table terutama digunakan untuk?', ['Merangkum data berdasarkan dimensi', 'Menulis backend API', 'Membuat CSS', 'Mengelola branch'], 'A'],
            'statistics-fundamentals' => ['Statistik untuk Membaca Data dengan Waras', 'Pelajari mean, median, sebaran, korelasi, dan sampling tanpa mengubah angka menjadi kepastian palsu.', 'Hitung mean, median, standar deviasi, lalu jelaskan kapan median lebih berguna.', 'Median lebih tahan terhadap apa dibanding mean?', ['Outlier ekstrem', 'Nama kolom', 'Warna chart', 'Ukuran file'], 'A'],
            'data-cleaning' => ['Membersihkan Data Sebelum Percaya Hasil', 'Periksa tipe data, missing value, duplikasi, format, dan outlier sebelum analisis.', 'Buat checklist cleaning untuk dataset transaksi dan dokumentasikan setiap perubahan.', 'Langkah awal yang masuk akal saat menemukan missing value adalah?', ['Memahami pola dan konteksnya', 'Selalu mengisi nol', 'Menghapus seluruh dataset', 'Mengubah semua menjadi teks'], 'A'],
            'python-data' => ['Python untuk Alur Analisis', 'Gunakan Python untuk membaca data, transformasi, fungsi kecil, dan otomasi pekerjaan berulang.', 'Baca file CSV, validasi kolom wajib, lalu hitung ringkasan sederhana.', 'Library standar Python untuk membaca file CSV adalah?', ['csv', 'css', 'httpd-only', 'git'], 'A'],
            'pandas' => ['Pandas untuk DataFrame', 'Kuasai filter, groupby, merge, missing value, dan reshape dengan perubahan yang mudah dilacak.', 'Gabungkan dua DataFrame berdasarkan customer_id lalu hitung total transaksi per customer.', 'Method Pandas yang umum untuk menggabungkan DataFrame berdasarkan key adalah?', ['merge', 'paint', 'route', 'deploy'], 'A'],
            'data-visualization' => ['Visualisasi yang Menjawab Pertanyaan', 'Pilih chart berdasarkan hubungan yang ingin ditunjukkan, bukan karena bentuknya menarik.', 'Buat tiga chart dari satu dataset dan tulis satu kalimat insight untuk masing-masing.', 'Chart apa yang umum dipakai untuk membandingkan kategori?', ['Bar chart', 'Scatter plot waktu tanpa sumbu', 'Captcha', 'Terminal tree'], 'A'],
            'sql-analytics' => ['SQL untuk Analisis yang Lebih Dalam', 'Gunakan CTE dan window function untuk ranking, running total, serta perbandingan antar periode.', 'Tulis query ranking produk per kategori berdasarkan total penjualan.', 'Window function memungkinkan perhitungan tanpa melakukan apa pada setiap baris hasil?', ['Menggabungkan seluruh hasil menjadi satu baris', 'Menghapus tabel', 'Mengubah CSS', 'Membuat token'], 'A'],
        ];

        foreach ($materials as $slug => [$title, $summary, $practice, $quiz, $options, $answer]) {
            $skill = $skills[$slug];

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

    private function seedAssessments(
        array $careers,
        array $skills,
    ): array {
        $sets = [
            'backend' => [
                'programming-fundamentals' => ['Apa keluaran yang paling mungkin dari kondisi if ketika ekspresinya bernilai false?', ['Blok if tetap dijalankan', 'Blok if dilewati', 'Program selalu berhenti', 'Database dihapus'], 'B'],
                'git-github' => ['Anda ingin menyimpan snapshot perubahan lokal sebelum push. Langkah yang tepat?', ['git commit setelah staging', 'git clone setiap kali', 'git reset --hard selalu', 'hapus folder .git'], 'A'],
                'http-fundamentals' => ['Client mengirim data untuk membuat user baru. Method yang paling tepat?', ['GET', 'POST', 'HEAD', 'OPTIONS'], 'B'],
                'database-fundamentals' => ['Satu user dapat memiliki banyak task. Relasi yang paling tepat?', ['One-to-many', 'One-to-one wajib', 'Tidak perlu relasi', 'Many-to-many selalu'], 'A'],
                'sql' => ['Anda ingin mengambil task beserta nama user dari dua tabel. Konsep SQL yang dibutuhkan?', ['JOIN', 'TRUNCATE', 'DROP DATABASE', 'GRANT saja'], 'A'],
                'php-laravel' => ['Di Laravel, route biasanya meneruskan request ke?', ['Controller atau handler', 'File gambar', 'CSS variable', 'Git branch'], 'A'],
                'eloquent-orm' => ['User::with("projects")->get() terutama digunakan untuk?', ['Eager loading relasi', 'Menghapus user', 'Membuat migration', 'Mengubah password server'], 'A'],
                'rest-api' => ['Endpoint DELETE /tasks/15 berhasil menghapus data tanpa body respons. Status yang cocok?', ['204', '404', '500', '101'], 'A'],
                'authentication-authorization' => ['User sudah login tetapi bukan admin. Pemeriksaan akses ke halaman admin termasuk?', ['Authorization', 'Authentication saja', 'Caching', 'Migration'], 'A'],
                'deployment-basics' => ['Secret production sebaiknya?', ['Disimpan di environment/secret manager', 'Ditaruh di repository publik', 'Ditulis di frontend bundle', 'Dikirim ke semua user'], 'A'],
            ],
            'frontend' => [
                'html-semantics' => ['Bagian navigasi utama sebaiknya menggunakan elemen?', ['nav', 'strong', 'canvas', 'code'], 'A'],
                'css-responsive' => ['Untuk layout kartu yang berubah kolom berdasarkan ruang, alat CSS yang relevan?', ['Grid atau Flexbox', 'SQL JOIN', 'Hashing password', 'Cron'], 'A'],
                'javascript' => ['await digunakan untuk?', ['Menunggu Promise pada async function', 'Menulis CSS', 'Membuat tabel SQL', 'Mengganti HTML menjadi gambar'], 'A'],
                'typescript' => ['Type union "draft" | "published" membantu karena?', ['Membatasi nilai status yang valid', 'Menghapus runtime', 'Membuat API otomatis', 'Mengganti database'], 'A'],
                'react' => ['Props pada React terutama digunakan untuk?', ['Mengirim data ke component', 'Membuat database', 'Menyimpan secret', 'Menjalankan migration'], 'A'],
                'state-management' => ['Filter yang hanya dipakai satu komponen paling sederhana disimpan di?', ['Local state', 'Global state wajib', 'Database production', 'DNS'], 'A'],
                'accessibility' => ['Tombol custom harus tetap dapat digunakan dengan?', ['Keyboard', 'Mouse saja', 'Resolusi 4K saja', 'Admin saja'], 'A'],
                'http-fundamentals' => ['Status 401 biasanya berkaitan dengan?', ['Kebutuhan autentikasi', 'CSS rusak', 'Query sukses', 'File terlalu kecil'], 'A'],
                'testing-fundamentals' => ['Test UI yang baik seharusnya memeriksa?', ['Perilaku yang dilihat pengguna', 'Nama variabel internal saja', 'Warna editor', 'Jumlah commit'], 'A'],
                'web-performance' => ['Gambar sangat besar di hero membuat halaman lambat. Tindakan awal yang masuk akal?', ['Optimalkan ukuran dan format', 'Tambah JavaScript acak', 'Hapus semantic HTML', 'Duplikasi gambar'], 'A'],
            ],
            'data' => [
                'spreadsheet-analysis' => ['Anda ingin merangkum penjualan berdasarkan kategori dengan cepat. Fitur yang cocok?', ['Pivot table', 'CSS Grid', 'Git merge', 'JWT'], 'A'],
                'statistics-fundamentals' => ['Dataset memiliki satu nilai ekstrem sangat besar. Ukuran pusat yang lebih tahan?', ['Median', 'Mean selalu', 'Range', 'Nama kolom'], 'A'],
                'data-cleaning' => ['Sebelum menghapus baris duplikat, hal pertama yang perlu dipastikan?', ['Definisi duplikat sesuai konteks data', 'Warna chart', 'Nama repository', 'Versi CSS'], 'A'],
                'database-fundamentals' => ['Primary key digunakan untuk?', ['Mengidentifikasi baris secara unik', 'Menggambar chart', 'Menyimpan warna', 'Mengirim email'], 'A'],
                'sql' => ['SUM dan COUNT termasuk?', ['Aggregate function', 'CSS selector', 'HTTP method', 'Git command'], 'A'],
                'sql-analytics' => ['Ranking dalam kelompok kategori dapat dibantu oleh?', ['Window function', 'HTML form', 'Cookie', 'Flexbox'], 'A'],
                'python-data' => ['List comprehension digunakan untuk?', ['Membentuk koleksi baru secara ringkas', 'Membuat index database', 'Mengatur DNS', 'Mengirim mail'], 'A'],
                'pandas' => ['DataFrame adalah struktur data dua dimensi yang umum pada?', ['Pandas', 'CSS', 'Git', 'Nginx'], 'A'],
                'data-visualization' => ['Untuk membandingkan nilai antar kategori, pilihan awal yang masuk akal?', ['Bar chart', 'Pie 50 kategori', 'Teks acak', 'Captcha'], 'A'],
                'git-github' => ['Mengapa analis data tetap berguna memahami Git?', ['Melacak perubahan script dan analisis', 'Menggantikan statistik', 'Membuat spreadsheet otomatis tanpa code', 'Menghapus kebutuhan data'], 'A'],
            ],
        ];

        $result = [];

        foreach ($sets as $key => $questions) {
            $assessment = Assessment::updateOrCreate(
                ['career_id' => $careers[$key]->id],
                [
                    'title' => 'Asesmen Awal '.$careers[$key]->name,
                    'description' => 'Jawab berdasarkan kemampuan saat ini. Skor objektif digabung dengan penilaian diri agar sistem memiliki titik awal yang lebih masuk akal.',
                    'duration_minutes' => 20,
                    'is_active' => true,
                ],
            );

            $assessment->questions()->delete();

            foreach ($questions as $slug => [$prompt, $options, $answer]) {
                AssessmentQuestion::create([
                    'assessment_id' => $assessment->id,
                    'skill_id' => $skills[$slug]->id,
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

            $result[$key] = $assessment;
        }

        return $result;
    }

    private function seedProjects(
        array $careers,
        array $skills,
    ): void {
        $projects = [
            ['backend', 'Task Management API', 'Pemula', 10, 'Bangun API pengelolaan tugas yang benar-benar punya autentikasi dan validasi.', 'Mahasiswa membutuhkan API sederhana untuk mengelola tugas pribadi secara aman.', ['Registrasi dan login', 'CRUD tugas', 'Status tugas', 'Validasi input', 'Relasi user dan task'], ['Filter dan pagination', 'Activity log'], ['Endpoint sesuai kontrak', 'User hanya mengakses tugas sendiri', 'Validation error konsisten'], ['rest-api' => 55, 'database-fundamentals' => 55, 'authentication-authorization' => 50, 'validation-error-handling' => 45]],
            ['backend', 'Sistem Reservasi Ruangan API', 'Menengah', 18, 'Bangun backend reservasi dengan aturan bentrok jadwal dan role.', 'Organisasi membutuhkan layanan reservasi ruangan yang menolak jadwal bentrok dan membedakan hak akses.', ['Manajemen user', 'Jadwal ruangan', 'Pemeriksaan bentrok', 'Role admin dan user', 'Riwayat reservasi'], ['Notifikasi', 'Audit log', 'Dokumentasi OpenAPI'], ['Tidak ada double booking', 'Hak akses diuji', 'Transaksi data aman'], ['rest-api' => 70, 'sql' => 65, 'authentication-authorization' => 65, 'testing-fundamentals' => 55]],
            ['frontend', 'Personal Finance Dashboard', 'Pemula', 12, 'Bangun dashboard keuangan yang tetap terbaca baik di mobile dan desktop.', 'Pengguna membutuhkan ringkasan transaksi yang cepat dipahami tanpa tabel yang membingungkan.', ['Daftar transaksi', 'Filter kategori', 'Ringkasan pemasukan dan pengeluaran', 'Chart bulanan', 'Responsive layout'], ['Dark mode', 'Export tampilan'], ['Keyboard navigation berfungsi', 'State filter konsisten', 'Layout responsif'], ['react' => 60, 'typescript' => 50, 'css-responsive' => 60, 'accessibility' => 45]],
            ['frontend', 'Accessible Event Planner', 'Menengah', 18, 'Bangun antarmuka pengelolaan event dengan form, state, dan pola aksesibilitas yang serius.', 'Panitia membutuhkan UI untuk membuat event dan mengelola peserta tanpa membingungkan pengguna keyboard.', ['Form event', 'Daftar peserta', 'Filter status', 'Dialog konfirmasi', 'Validasi UI'], ['Optimistic update', 'Skeleton loading'], ['Focus management benar', 'Error form jelas', 'State tidak hilang tanpa alasan'], ['react' => 70, 'state-management' => 60, 'accessibility' => 65, 'testing-fundamentals' => 50]],
            ['data', 'Analisis Kinerja Penjualan', 'Pemula', 10, 'Bersihkan dan analisis data penjualan lalu rangkum temuan yang relevan.', 'Manajer ingin mengetahui kategori, wilayah, dan periode yang paling berpengaruh pada penjualan.', ['Data cleaning', 'Ringkasan KPI', 'Analisis kategori', 'Analisis waktu', 'Visualisasi utama'], ['Segmentasi pelanggan', 'Analisis margin'], ['Setiap chart menjawab pertanyaan', 'Transformasi data terdokumentasi', 'Insight didukung angka'], ['data-cleaning' => 55, 'statistics-fundamentals' => 50, 'data-visualization' => 55, 'spreadsheet-analysis' => 50]],
            ['data', 'Customer Retention Analysis', 'Menengah', 18, 'Gunakan SQL dan Python untuk melihat pola pelanggan kembali dan perubahan antar periode.', 'Tim produk ingin memahami apakah pelanggan kembali bertransaksi setelah pembelian pertama.', ['Definisi cohort', 'Query transaksi', 'Retention table', 'Visualisasi retention', 'Ringkasan insight'], ['Segmentasi kanal', 'Perbandingan campaign'], ['Definisi metrik jelas', 'Query dapat direproduksi', 'Kesimpulan tidak melebihi data'], ['sql-analytics' => 65, 'pandas' => 60, 'statistics-fundamentals' => 60, 'data-visualization' => 60]],
        ];

        foreach ($projects as [
            $careerKey,
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
            $project = PortfolioProject::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'career_id' => $careers[$careerKey]->id,
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

            foreach ($requiredSkills as $slug => $level) {
                $sync[$skills[$slug]->id] = [
                    'required_level' => $level,
                    'weight' => 1,
                ];
            }

            $project->skills()->sync($sync);
        }
    }

    private function seedUsers(
        array $careers,
        array $skills,
        array $assessments,
    ): void {
        User::updateOrCreate(
            ['email' => 'admin@skillpath.test'],
            [
                'name' => 'Admin SkillPath',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'admin',
                'weekly_study_hours' => 6,
            ],
        );

        $demo = User::updateOrCreate(
            ['email' => 'demo@skillpath.test'],
            [
                'name' => 'Nadia Pratama',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'student',
                'study_program' => 'Sistem Informasi',
                'semester' => 5,
                'interest_area' => 'Backend dan pengembangan produk web',
                'experience' => 'Pernah membuat aplikasi CRUD sederhana dan menggunakan Git untuk tugas kuliah.',
                'weekly_study_hours' => 8,
                'target_career_id' => $careers['backend']->id,
                'onboarding_completed_at' => now(),
            ],
        );

        $scores = [
            'programming-fundamentals' => 80,
            'git-github' => 45,
            'terminal-cli' => 55,
            'http-fundamentals' => 70,
            'database-fundamentals' => 30,
            'sql' => 55,
            'php-laravel' => 65,
            'rest-api' => 55,
            'authentication-authorization' => 50,
            'validation-error-handling' => 45,
            'testing-fundamentals' => 45,
            'deployment-basics' => 10,
        ];

        $attempt = (string) Str::uuid();

        $demo->progressLogs()->delete();
        $demo->projects()->delete();
        $demo->assessmentResults()->delete();
        $demo->userSkills()->delete();

        foreach ($scores as $slug => $score) {
            UserSkill::updateOrCreate(
                [
                    'user_id' => $demo->id,
                    'skill_id' => $skills[$slug]->id,
                ],
                [
                    'score' => $score,
                    'source' => 'assessment',
                    'last_assessed_at' => now()->subDay(),
                ],
            );

            AssessmentResult::create([
                'user_id' => $demo->id,
                'assessment_id' => $assessments['backend']->id,
                'skill_id' => $skills[$slug]->id,
                'attempt_uuid' => $attempt,
                'score' => $score,
                'is_correct' => $score >= 70,
                'self_rating' => min($score, 100),
                'answer' => null,
            ]);
        }

        $demo->roadmaps()->delete();
        $demo->readinessSnapshots()->delete();

        $freshDemo = $demo->fresh(['targetCareer']);

        app(RoadmapService::class)->regenerate(
            $freshDemo,
            'Data demo asesmen awal',
        );

        app(CareerReadinessService::class)->snapshot(
            $freshDemo,
            'demo_seed',
        );
    }
}
