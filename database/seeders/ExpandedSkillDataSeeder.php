<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\Career;
use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExpandedSkillDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->attachCareerSkills();
        $this->attachPrerequisites();
        $this->createLearningMaterials();
        $this->createAssessmentQuestions();
        $this->attachProjectSkills();
    }

    private function attachCareerSkills(): void
    {
        $maps = [
            'backend-developer' => [
                'data-structures-algorithms' => [70, 1.20],
                'database-performance' => [65, 1.00],
                'api-documentation' => [65, 0.95],
                'caching-strategies' => [60, 0.90],
            ],
            'frontend-developer' => [
                'browser-dom-events' => [75, 1.15],
                'component-architecture' => [75, 1.20],
                'frontend-testing' => [65, 1.00],
            ],
            'data-analyst' => [
                'database-performance' => [55, 0.80],
                'business-metrics-kpi' => [75, 1.25],
                'exploratory-data-analysis' => [80, 1.40],
                'data-storytelling' => [75, 1.20],
            ],
        ];

        $skills = Skill::query()
            ->get()
            ->keyBy('slug');

        foreach ($maps as $careerSlug => $mapping) {
            $career = Career::query()
                ->where('slug', $careerSlug)
                ->firstOrFail();

            $sync = [];

            foreach ($mapping as $skillSlug => [$target, $weight]) {
                $skill = $skills->get($skillSlug);

                if (! $skill instanceof Skill) {
                    continue;
                }

                $sync[$skill->id] = [
                    'target_level' => $target,
                    'importance_weight' => $weight,
                    'is_required' => $weight >= 0.90,
                ];
            }

            $career->skills()->syncWithoutDetaching(
                $sync,
            );
        }
    }

    private function attachPrerequisites(): void
    {
        $pairs = [
            ['data-structures-algorithms', 'programming-fundamentals'],
            ['database-performance', 'database-fundamentals'],
            ['database-performance', 'sql'],
            ['api-documentation', 'rest-api'],
            ['caching-strategies', 'rest-api'],
            ['caching-strategies', 'database-performance'],
            ['browser-dom-events', 'html-semantics'],
            ['browser-dom-events', 'javascript'],
            ['component-architecture', 'react'],
            ['component-architecture', 'typescript'],
            ['frontend-testing', 'react'],
            ['frontend-testing', 'testing-fundamentals'],
            ['business-metrics-kpi', 'spreadsheet-analysis'],
            ['business-metrics-kpi', 'statistics-fundamentals'],
            ['exploratory-data-analysis', 'data-cleaning'],
            ['exploratory-data-analysis', 'statistics-fundamentals'],
            ['data-storytelling', 'data-visualization'],
            ['data-storytelling', 'business-metrics-kpi'],
        ];

        $skills = Skill::query()
            ->get()
            ->keyBy('slug');

        foreach ($pairs as [$skillSlug, $prerequisiteSlug]) {
            $skill = $skills->get($skillSlug);
            $prerequisite = $skills->get(
                $prerequisiteSlug,
            );

            if (
                ! $skill instanceof Skill
                || ! $prerequisite instanceof Skill
            ) {
                continue;
            }

            DB::table('skill_prerequisites')
                ->updateOrInsert(
                    [
                        'skill_id' => $skill->id,
                        'prerequisite_skill_id' => $prerequisite->id,
                    ],
                    [
                        'factor' => 1.20,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                );
        }
    }

    private function createLearningMaterials(): void
    {
        $materials = [
            'data-structures-algorithms' => [
                'Struktur Data untuk Memecah Masalah',
                'Pelajari cara memilih struktur data berdasarkan pola akses dan kebutuhan operasi, bukan sekadar menghafal istilah.',
                'Implementasikan stack dan queue sederhana lalu jelaskan perbedaan pola akses keduanya.',
                'Struktur data mana yang mengikuti prinsip FIFO?',
                [
                    'Queue',
                    'Stack',
                    'Tree selalu',
                    'Hash password',
                ],
                'A',
            ],
            'database-performance' => [
                'Membaca Query dan Indeks',
                'Kenali query mahal, fungsi indeks, dan trade-off antara kecepatan baca dengan biaya penyimpanan serta penulisan.',
                'Bandingkan satu query sebelum dan sesudah indeks yang relevan lalu catat perubahan execution plan atau waktu eksekusi.',
                'Tujuan utama indeks basis data adalah?',
                [
                    'Mempercepat pencarian tertentu dengan struktur tambahan',
                    'Menghapus kebutuhan query',
                    'Menggantikan primary key selalu',
                    'Membuat semua write lebih cepat tanpa biaya',
                ],
                'A',
            ],
            'api-documentation' => [
                'Dokumentasi API yang Dapat Dipakai',
                'Dokumentasikan kontrak endpoint agar pengguna API memahami request, response, autentikasi, dan kondisi gagal tanpa menebak.',
                'Dokumentasikan tiga endpoint proyek lengkap dengan method, path, parameter, contoh request, response sukses, dan response error.',
                'Dokumentasi endpoint yang baik paling tidak perlu menjelaskan?',
                [
                    'Method, path, input, output, dan kemungkinan error',
                    'Warna editor developer',
                    'Jumlah commit setiap hari',
                    'Password server',
                ],
                'A',
            ],
            'caching-strategies' => [
                'Caching Tanpa Data Basi',
                'Gunakan cache untuk beban yang tepat dan pahami invalidasi, TTL, serta cache key sebelum mengejar performa.',
                'Tambahkan cache pada satu query baca yang sering dipakai, tentukan TTL, lalu jelaskan kapan cache harus dihapus atau diperbarui.',
                'Risiko utama cache yang tidak diinvalidasi dengan benar adalah?',
                [
                    'Data basi tetap disajikan',
                    'CSS otomatis hilang',
                    'Semua query menjadi transaksi',
                    'Git tidak dapat commit',
                ],
                'A',
            ],
            'browser-dom-events' => [
                'DOM dan Event di Browser',
                'Pahami event target, bubbling, default action, form event, dan interaksi browser sebelum menyerahkan seluruh perilaku pada framework.',
                'Buat form kecil dengan submit, validasi client-side, dan satu event click lalu jelaskan kapan preventDefault diperlukan.',
                'event.preventDefault() digunakan untuk?',
                [
                    'Mencegah aksi default browser untuk event tersebut',
                    'Menghapus semua event listener',
                    'Mengganti database',
                    'Membuat route backend',
                ],
                'A',
            ],
            'component-architecture' => [
                'Arsitektur Komponen yang Mudah Dirawat',
                'Bagi UI berdasarkan tanggung jawab, data flow, dan kebutuhan reuse tanpa membuat abstraction terlalu dini.',
                'Refactor satu halaman besar menjadi beberapa komponen dengan props yang jelas dan jelaskan alasan setiap batas komponen.',
                'Composition pada komponen terutama membantu?',
                [
                    'Menyusun perilaku dan UI dari bagian yang lebih kecil',
                    'Menghapus kebutuhan TypeScript',
                    'Mengganti HTTP',
                    'Menyimpan secret di frontend',
                ],
                'A',
            ],
            'frontend-testing' => [
                'Testing Frontend dari Sudut Pandang Pengguna',
                'Uji interaksi dan hasil yang terlihat pengguna sehingga refactor internal tidak mudah merusak test yang sebenarnya masih valid.',
                'Tulis test untuk form yang mencakup input valid, error validasi, dan keberhasilan submit dari perspektif pengguna.',
                'Pengujian frontend yang baik sebaiknya paling banyak berfokus pada?',
                [
                    'Perilaku yang dapat diamati pengguna',
                    'Nama function internal saja',
                    'Jumlah file CSS',
                    'Urutan import tanpa konteks',
                ],
                'A',
            ],
            'business-metrics-kpi' => [
                'Metrik Bisnis yang Tidak Menyesatkan',
                'Definisikan numerator, denominator, periode, segmentasi, dan tujuan keputusan sebelum menghitung KPI.',
                'Pilih satu KPI produk, tulis definisinya secara eksplisit, lalu hitung dari dataset kecil dengan periode yang jelas.',
                'Mengapa definisi denominator penting pada KPI?',
                [
                    'Karena menentukan dasar perbandingan dan arti angka',
                    'Karena mengganti warna chart',
                    'Karena menghapus missing value otomatis',
                    'Karena membuat API endpoint',
                ],
                'A',
            ],
            'exploratory-data-analysis' => [
                'EDA Sebelum Menarik Kesimpulan',
                'Gunakan distribusi, segmentasi, korelasi, dan pemeriksaan anomali untuk memahami data sebelum menyusun narasi.',
                'Lakukan EDA pada dataset kecil: periksa distribusi utama, missing value, outlier, dan dua hubungan antarkolom lalu catat temuan awal.',
                'Tujuan utama exploratory data analysis adalah?',
                [
                    'Memahami pola dan masalah data sebelum kesimpulan final',
                    'Membuktikan hipotesis apa pun pasti benar',
                    'Menghilangkan kebutuhan cleaning',
                    'Mengganti semua analisis dengan chart',
                ],
                'A',
            ],
            'data-storytelling' => [
                'Menyampaikan Data sebagai Argumen yang Dapat Diuji',
                'Hubungkan pertanyaan, bukti, visualisasi, konteks, keterbatasan, dan tindakan tanpa melebih-lebihkan hasil.',
                'Susun satu halaman insight yang memuat pertanyaan, dua visualisasi, tiga temuan, satu keterbatasan, dan satu rekomendasi tindakan.',
                'Kesimpulan dalam data storytelling seharusnya?',
                [
                    'Didukung data dan menyebut keterbatasan yang relevan',
                    'Selalu dibuat dramatis',
                    'Mengabaikan konteks bisnis',
                    'Tidak perlu terkait visualisasi',
                ],
                'A',
            ],
        ];

        $skills = Skill::query()
            ->whereIn(
                'slug',
                array_keys($materials),
            )
            ->get()
            ->keyBy('slug');

        foreach (
            $materials as $skillSlug => [
                $title,
                $summary,
                $practice,
                $quiz,
                $options,
                $answer,
            ]
        ) {
            $skill = $skills->get($skillSlug);

            if (! $skill instanceof Skill) {
                continue;
            }

            $core = LearningMaterial::updateOrCreate(
                [
                    'slug' => Str::slug($title),
                ],
                [
                    'skill_id' => $skill->id,
                    'material_type' => 'core',
                    'reinforcement_for_material_id' => null,
                    'is_active' => true,
                    'title' => $title,
                    'summary' => $summary,
                    'learning_objectives' => [
                        'Memahami konsep inti '.$skill->name,
                        'Menerapkan konsep pada latihan yang dapat diperiksa',
                        'Menjelaskan alasan di balik solusi atau analisis yang dipilih',
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

            LearningMaterial::updateOrCreate(
                [
                    'slug' => 'penguatan-'.$core->slug,
                ],
                [
                    'skill_id' => $skill->id,
                    'material_type' => 'reinforcement',
                    'reinforcement_for_material_id' => $core->id,
                    'is_active' => true,
                    'title' => 'Penguatan: '.$core->title,
                    'summary' => 'Materi penguatan untuk mengulang konsep penting sebelum mencoba evaluasi materi utama kembali.',
                    'learning_objectives' => [
                        'Mengulang konsep yang masih lemah',
                        'Mengerjakan versi latihan yang lebih terarah',
                        'Menyiapkan diri untuk evaluasi ulang',
                    ],
                    'difficulty' => $core->difficulty,
                    'estimated_minutes' => 60,
                    'resource_title' => $core->resource_title,
                    'resource_url' => $core->resource_url,
                    'practice_task' => 'Ulangi dengan ruang lingkup lebih kecil: '.$core->practice_task,
                    'quiz_question' => $core->quiz_question,
                    'quiz_options' => $core->quiz_options,
                    'quiz_answer' => $core->quiz_answer,
                    'quiz_explanation' => $core->quiz_explanation,
                ],
            );
        }
    }

    private function createAssessmentQuestions(): void
    {
        $questions = [
            'backend-developer' => [
                'data-structures-algorithms' => [
                    'case',
                    'Anda membutuhkan struktur data yang memproses item sesuai urutan kedatangan. Pilihan paling tepat?',
                    [
                        'Queue',
                        'Stack',
                        'Set tanpa urutan selalu',
                        'String',
                    ],
                    'A',
                    false,
                ],
                'database-performance' => [
                    'case',
                    'Query pencarian berdasarkan email sangat sering dijalankan pada tabel besar. Langkah awal yang masuk akal?',
                    [
                        'Periksa execution plan dan pertimbangkan indeks pada kolom yang relevan',
                        'Tambahkan loop di aplikasi',
                        'Hapus constraint',
                        'Duplikasi semua baris',
                    ],
                    'A',
                    false,
                ],
                'api-documentation' => [
                    'practical',
                    'Tim frontend kesulitan memakai endpoint karena kontraknya tidak jelas. Informasi paling penting untuk ditambahkan?',
                    [
                        'Method, path, input, output, auth, dan error',
                        'Tema editor backend',
                        'Nama laptop developer',
                        'Riwayat browsing',
                    ],
                    'A',
                    true,
                ],
                'caching-strategies' => [
                    'case',
                    'Data profil berubah tetapi halaman masih menampilkan nilai lama dari cache. Masalah utama yang perlu diperiksa?',
                    [
                        'Strategi invalidasi cache',
                        'Nama branch Git',
                        'Warna tombol',
                        'Ukuran font',
                    ],
                    'A',
                    false,
                ],
            ],
            'frontend-developer' => [
                'browser-dom-events' => [
                    'practical',
                    'Form melakukan reload halaman padahal ingin ditangani dengan JavaScript. Mekanisme yang relevan?',
                    [
                        'Mencegah default action pada event submit',
                        'Menghapus DOM',
                        'Mengganti database',
                        'Menambah migration',
                    ],
                    'A',
                    true,
                ],
                'component-architecture' => [
                    'case',
                    'Satu komponen memiliki terlalu banyak tanggung jawab dan sulit diuji. Perbaikan yang paling masuk akal?',
                    [
                        'Pisahkan berdasarkan tanggung jawab dan data flow yang jelas',
                        'Pindahkan semua state ke global tanpa alasan',
                        'Hapus type',
                        'Gabungkan semua file menjadi satu',
                    ],
                    'A',
                    false,
                ],
                'frontend-testing' => [
                    'practical',
                    'Test form sebaiknya memverifikasi apa?',
                    [
                        'Interaksi dan hasil yang dapat diamati pengguna',
                        'Nama function internal saja',
                        'Jumlah file CSS',
                        'Ukuran monitor',
                    ],
                    'A',
                    true,
                ],
            ],
            'data-analyst' => [
                'business-metrics-kpi' => [
                    'case',
                    'Conversion rate berubah tajam setelah denominator didefinisikan berbeda. Apa yang harus dilakukan?',
                    [
                        'Samakan definisi numerator, denominator, dan periode sebelum membandingkan',
                        'Pilih angka yang lebih tinggi',
                        'Hapus data lama',
                        'Ubah warna chart',
                    ],
                    'A',
                    false,
                ],
                'exploratory-data-analysis' => [
                    'practical',
                    'Sebelum menyimpulkan penyebab penurunan penjualan, langkah yang lebih tepat?',
                    [
                        'Eksplorasi distribusi, segmentasi, anomali, dan kualitas data',
                        'Langsung memilih satu penyebab',
                        'Menghapus outlier tanpa pemeriksaan',
                        'Membuat presentasi sebelum analisis',
                    ],
                    'A',
                    true,
                ],
                'data-storytelling' => [
                    'practical',
                    'Narasi data yang bertanggung jawab harus menghubungkan?',
                    [
                        'Pertanyaan, bukti, visualisasi, keterbatasan, dan tindakan',
                        'Chart sebanyak mungkin tanpa konteks',
                        'Opini tanpa data',
                        'Warna dan animasi saja',
                    ],
                    'A',
                    true,
                ],
                'database-performance' => [
                    'case',
                    'Query analitik pada tabel besar berjalan lambat. Pemeriksaan awal yang paling masuk akal?',
                    [
                        'Periksa execution plan, filter, join, dan indeks yang relevan',
                        'Tambah gambar pada dashboard',
                        'Hapus semua primary key',
                        'Ubah query menjadi CSS',
                    ],
                    'A',
                    false,
                ],
            ],
        ];

        $skills = Skill::query()
            ->get()
            ->keyBy('slug');

        foreach ($questions as $careerSlug => $careerQuestions) {
            $career = Career::query()
                ->where('slug', $careerSlug)
                ->firstOrFail();

            $assessment = Assessment::query()
                ->where('career_id', $career->id)
                ->where('is_active', true)
                ->firstOrFail();

            $assessment->update([
                'duration_minutes' => 30,
            ]);

            foreach (
                $careerQuestions as $skillSlug => [
                    $questionType,
                    $prompt,
                    $options,
                    $answer,
                    $evidenceRequired,
                ]
            ) {
                $skill = $skills->get($skillSlug);

                if (! $skill instanceof Skill) {
                    continue;
                }

                $material = LearningMaterial::query()
                    ->where('skill_id', $skill->id)
                    ->where('material_type', 'core')
                    ->where('is_active', true)
                    ->first();

                AssessmentQuestion::updateOrCreate(
                    [
                        'assessment_id' => $assessment->id,
                        'skill_id' => $skill->id,
                    ],
                    [
                        'question_type' => $questionType,
                        'prompt' => $prompt,
                        'practical_instructions' => $questionType === 'practical'
                            ? $material?->practice_task
                            : null,
                        'evidence_required' => $evidenceRequired,
                        'options' => [
                            'A' => $options[0],
                            'B' => $options[1],
                            'C' => $options[2],
                            'D' => $options[3],
                        ],
                        'correct_answer' => $answer,
                        'explanation' => 'Gunakan konsep dasar skill ini untuk menentukan jawaban dan jelaskan penerapannya pada konteks yang diberikan.',
                        'difficulty' => $skill->difficulty,
                    ],
                );
            }
        }
    }

    private function attachProjectSkills(): void
    {
        $maps = [
            'sistem-reservasi-ruangan-api' => [
                'api-documentation' => 55,
                'database-performance' => 50,
            ],
            'personal-finance-dashboard' => [
                'browser-dom-events' => 50,
            ],
            'accessible-event-planner' => [
                'component-architecture' => 55,
                'frontend-testing' => 55,
            ],
            'analisis-kinerja-penjualan' => [
                'business-metrics-kpi' => 55,
                'exploratory-data-analysis' => 55,
                'data-storytelling' => 45,
            ],
            'customer-retention-analysis' => [
                'business-metrics-kpi' => 60,
                'exploratory-data-analysis' => 60,
                'data-storytelling' => 50,
                'database-performance' => 45,
            ],
        ];

        $skills = Skill::query()
            ->get()
            ->keyBy('slug');

        foreach ($maps as $projectSlug => $requirements) {
            $project = PortfolioProject::query()
                ->where('slug', $projectSlug)
                ->first();

            if (! $project instanceof PortfolioProject) {
                continue;
            }

            $sync = [];

            foreach ($requirements as $skillSlug => $requiredLevel) {
                $skill = $skills->get($skillSlug);

                if (! $skill instanceof Skill) {
                    continue;
                }

                $sync[$skill->id] = [
                    'required_level' => $requiredLevel,
                    'weight' => 1,
                ];
            }

            $project->skills()->syncWithoutDetaching(
                $sync,
            );
        }
    }
}
