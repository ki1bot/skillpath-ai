<?php

namespace Database\Seeders;

use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Models\Skill;
use App\Support\AcademicProgramCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AcademicTaskRevisionSeeder extends Seeder
{
    private const STAGES = [
        'amatir' => 'Amatir',
        'menengah' => 'Menengah',
        'ahli' => 'Ahli',
    ];

    public function run(): void
    {
        DB::transaction(function (): void {
            $this->reviseMaterials();
            $this->reviseProjects();
        });
    }

    private function reviseMaterials(): void
    {
        $skills = Skill::query()
            ->whereIn(
                'slug',
                AcademicProgramCatalog::allSkillSlugs(),
            )
            ->get()
            ->keyBy('slug');

        foreach (AcademicProgramCatalog::programs() as $programName => $program) {
            foreach ($program['areas'] as $area) {
                $areaName = (string) $area['name'];

                foreach ($area['skills'] as $skillDefinition) {
                    $skillSlug = (string) $skillDefinition['slug'];
                    $skill = $skills->get($skillSlug);

                    if (! $skill) {
                        throw new RuntimeException(
                            'Kemampuan '.$skillSlug.' tidak ditemukan.',
                        );
                    }

                    $source = LearningMaterial::query()
                        ->where('slug', 'belajar-'.$skillSlug)
                        ->firstOrFail();

                    if ((int) $source->skill_id !== (int) $skill->id) {
                        throw new RuntimeException(
                            'Materi sumber '.$skillSlug.' terhubung dengan kemampuan yang salah.',
                        );
                    }

                    $baseTask = trim(
                        (string) $source->practice_task,
                    );

                    if ($baseTask === '') {
                        throw new RuntimeException(
                            'Tugas dasar '.$skillSlug.' tidak boleh kosong.',
                        );
                    }

                    foreach (self::STAGES as $stage => $stageTitle) {
                        $core = LearningMaterial::query()
                            ->where(
                                'slug',
                                'belajar-'.$stage.'-'.$skillSlug,
                            )
                            ->firstOrFail();

                        $reinforcement = LearningMaterial::query()
                            ->where(
                                'slug',
                                'penguatan-'.$stage.'-'.$skillSlug,
                            )
                            ->firstOrFail();

                        if (
                            (int) $core->skill_id !== (int) $skill->id
                            || $core->material_type !== 'core'
                            || $core->difficulty !== $stageTitle
                            || (int) $reinforcement->skill_id !== (int) $skill->id
                            || $reinforcement->material_type !== 'reinforcement'
                            || $reinforcement->difficulty !== $stageTitle
                            || (int) $reinforcement->reinforcement_for_material_id !== (int) $core->id
                        ) {
                            throw new RuntimeException(
                                'Relasi materi '.$skillSlug.' tahap '.$stageTitle.' tidak sesuai katalog akademik.',
                            );
                        }

                        $task = $this->materialTask(
                            (string) $programName,
                            $areaName,
                            (string) $skill->name,
                            $stage,
                            $baseTask,
                        );

                        $oldCorePrefix = match ($stage) {
                            'amatir' => 'Kerjakan tugas dasar '.$skill->name.' berikut.',
                            'menengah' => 'Pada tahap Menengah, gunakan kemampuan '.$skill->name,
                            'ahli' => 'Pada tahap Ahli, kerjakan '.$skill->name,
                        };

                        if (str_starts_with(
                            trim((string) $core->practice_task),
                            $oldCorePrefix,
                        )) {
                            $core->update([
                                'practice_task' => $task,
                            ]);
                        }

                        $oldReinforcementPrefix = 'Buka kembali hasil tugas '
                            .$stageTitle
                            .' untuk '
                            .$skill->name;

                        if (str_starts_with(
                            trim((string) $reinforcement->practice_task),
                            $oldReinforcementPrefix,
                        )) {
                            $reinforcement->update([
                                'practice_task' => $this->reinforcementTask(
                                    (string) $programName,
                                    $areaName,
                                    (string) $skill->name,
                                    $stageTitle,
                                    (string) $core->practice_task,
                                ),
                            ]);
                        }
                    }
                }
            }
        }
    }

    private function materialTask(
        string $program,
        string $area,
        string $skill,
        string $stage,
        string $baseTask,
    ): string {
        $stageTitle = self::STAGES[$stage];

        $instructions = match ($stage) {
            'amatir' => implode("\n", [
                '1. Baca kasus di atas dan tentukan hasil yang perlu dibuat.',
                '2. Kerjakan bagian utama dengan contoh atau data sederhana yang sesuai dengan topik.',
                '3. Periksa apakah hasilnya sudah sesuai dengan ketentuan tugas.',
                '4. Jelaskan secara singkat cara kamu mengerjakannya.',
            ]),

            'menengah' => implode("\n", [
                '1. Kerjakan kasus tersebut dengan data atau situasi yang lebih bervariasi.',
                '2. Terapkan kemampuan yang sedang dipelajari tanpa hanya menyalin contoh.',
                '3. Periksa hasilnya menggunakan sedikitnya dua kondisi yang relevan.',
                '4. Jelaskan masalah yang ditemukan dan cara kamu memperbaikinya.',
                '5. Tuliskan alasan memilih pendekatan yang digunakan.',
            ]),

            'ahli' => implode("\n", [
                '1. Kerjakan kasus tersebut sebagai simulasi pekerjaan yang mendekati kondisi nyata.',
                '2. Tentukan batasan atau kendala yang masuk akal untuk kasus ini.',
                '3. Periksa hasil pada kondisi normal, kondisi batas, dan kondisi yang berpotensi menimbulkan masalah.',
                '4. Dokumentasikan hasil pemeriksaan serta perbaikan yang dilakukan.',
                '5. Jelaskan kelebihan, keterbatasan, dan pengembangan yang masih mungkin dilakukan.',
            ]),
        };

        return implode("\n\n", [
            'Tugas '.$stageTitle.' — '.$skill,
            'Jurusan: '.$program."\n".'Bidang: '.$area,
            'Kasus yang dikerjakan'."\n".$baseTask,
            'Langkah pengerjaan'."\n".$instructions,
            'Hasil yang dikumpulkan'."\n"
                .'Simpan hasil pekerjaan, bukti pemeriksaan yang relevan, dan penjelasan singkat dalam satu folder Google Drive. Pastikan folder dapat dibuka oleh administrator.',
        ]);
    }

    private function reinforcementTask(
        string $program,
        string $area,
        string $skill,
        string $stage,
        string $originalTask,
    ): string {
        return implode("\n\n", [
            'Perbaikan tugas '.$stage.' — '.$skill,
            'Jurusan: '.$program."\n".'Bidang: '.$area,
            'Yang perlu dilakukan'."\n"
                .'Buka kembali pekerjaan yang sudah dikumpulkan. Baca catatan administrator, lalu tandai bagian yang masih perlu diperbaiki.',
            'Perbaiki bagian tersebut tanpa mengulang pekerjaan yang sudah benar. Setelah selesai, periksa kembali hasilnya dan catat perubahan yang kamu lakukan.',
            'Acuan tugas'."\n".$originalTask,
            'Pengumpulan'."\n"
                .'Simpan hasil revisi dan ringkasan perubahan dalam folder Google Drive untuk diperiksa kembali.',
        ]);
    }

    private function reviseProjects(): void
    {
        foreach ($this->projectBriefs() as $slug => $definition) {
            $project = PortfolioProject::query()
                ->where('slug', $slug)
                ->with([
                    'career',
                    'skills',
                ])
                ->firstOrFail();

            $career = $definition['career'];
            $area = $definition['area'];

            if ($project->career?->name !== $career) {
                throw new RuntimeException(
                    'Proyek '.$slug.' terhubung dengan jurusan yang salah.',
                );
            }

            $expectedSkills = AcademicProgramCatalog::areaSkillSlugs(
                $career,
                $area,
            );

            $actualSkills = $project
                ->skills
                ->pluck('slug')
                ->sort()
                ->values()
                ->all();

            sort($expectedSkills);

            if (
                count($expectedSkills) !== 3
                || $actualSkills !== $expectedSkills
            ) {
                throw new RuntimeException(
                    'Kemampuan proyek '.$slug.' tidak sesuai dengan bidang '.$area.' pada jurusan '.$career.'.',
                );
            }

            if (str_starts_with(
                trim((string) $project->problem_statement),
                $definition['original'],
            )) {
                $project->update([
                    'problem_statement' => $definition['brief'],
                ]);
            }
        }
    }

    private function projectBriefs(): array
    {
        return [
            'sales-business-intelligence-dashboard' => [
                'career' => 'Sistem Informasi',
                'area' => 'Analisis Data',
                'original' => 'Sebuah perusahaan memiliki data transaksi penjualan',
                'brief' => 'Sebuah toko memiliki data penjualan, tetapi pemiliknya kesulitan mengetahui produk terlaris dan perkembangan pendapatan setiap bulan. Bantulah toko tersebut menyiapkan laporan yang mudah dipahami. Rapikan data, olah menggunakan SQL dan spreadsheet, lalu tampilkan hasilnya dalam dashboard. Dari hasil analisis, jelaskan apa yang sedang terjadi pada penjualan dan keputusan apa yang bisa dipertimbangkan pemilik toko.',
            ],

            'build-mini-information-system' => [
                'career' => 'Sistem Informasi',
                'area' => 'Pengembangan Sistem',
                'original' => 'Pilih satu kasus yang cukup sederhana tetapi nyata',
                'brief' => 'Sebuah organisasi masih mencatat kegiatan sehari-hari secara manual. Pilih satu masalah yang bisa dibantu dengan sistem informasi, misalnya pencatatan inventaris, peminjaman barang, atau penjualan. Cari tahu kebutuhan penggunanya, rancang struktur database, kemudian buat aplikasi sederhana untuk mengelola data tersebut. Pastikan pengguna dapat menjalankan fungsi utama dan jelaskan bagaimana sistem membantu pekerjaan mereka.',
            ],

            'redesign-digital-product' => [
                'career' => 'Sistem Informasi',
                'area' => 'UI/UX',
                'original' => 'Pilih satu aplikasi atau layanan digital',
                'brief' => 'Pilih sebuah aplikasi atau website yang menurutmu masih membingungkan saat digunakan. Cari tahu kesulitan yang dialami penggunanya melalui pengamatan atau riset sederhana. Setelah itu, buat rancangan tampilan dan alur penggunaan yang lebih jelas. Uji prototipe dengan beberapa skenario tugas, catat tanggapan pengguna, dan jelaskan perubahan desain yang paling membantu.',
            ],

            'digital-marketing-campaign' => [
                'career' => 'Manajemen',
                'area' => 'Marketing',
                'original' => 'Pilih produk atau layanan yang jelas',
                'brief' => 'Bayangkan kamu diminta membantu usaha kecil memperkenalkan produknya melalui media digital. Kenali calon pembelinya, cari tahu kebutuhan mereka, dan pelajari cara pesaing memasarkan produk serupa. Berdasarkan temuan tersebut, susun identitas merek, pesan promosi, serta rencana kampanye digital. Jelaskan alasan pemilihan strategi dan bagaimana hasil kampanye akan diukur.',
            ],

            'financial-health-analysis' => [
                'career' => 'Manajemen',
                'area' => 'Keuangan',
                'original' => 'Gunakan laporan keuangan sederhana',
                'brief' => 'Sebuah usaha ingin mengetahui apakah kondisi keuangannya cukup sehat untuk berkembang. Gunakan laporan keuangan sederhana atau data simulasi yang dinyatakan dengan jelas. Analisis pendapatan, pengeluaran, arus kas, dan indikator keuangan yang relevan. Setelah itu, susun rencana perbaikan serta pertimbangan investasi berdasarkan hasil perhitungan, bukan perkiraan semata.',
            ],

            'recruitment-strategy' => [
                'career' => 'Manajemen',
                'area' => 'Human Resources',
                'original' => 'Bayangkan sebuah perusahaan sedang menambah',
                'brief' => 'Sebuah perusahaan membutuhkan karyawan baru untuk mendukung perkembangan bisnisnya. Pilih satu posisi yang akan direkrut, lalu tentukan tugas, persyaratan, dan cara menilai kandidatnya. Buat tahapan seleksi yang jelas dan adil. Setelah itu, susun cara menilai kinerja karyawan serta rencana pengembangan kemampuan mereka setelah diterima bekerja.',
            ],

            'software-development-project' => [
                'career' => 'Teknik Informatika',
                'area' => 'Pemrograman dan Rekayasa Perangkat Lunak',
                'original' => 'Buat aplikasi yang mempunyai masalah jelas',
                'brief' => 'Buat aplikasi sederhana untuk menyelesaikan satu masalah sehari-hari, seperti pencatatan tugas, peminjaman barang, atau pengelolaan transaksi. Rancang alur program sebelum mulai menulis kode. Gunakan algoritma dan konsep pemrograman berorientasi objek yang sesuai. Setelah aplikasi berjalan, uji fungsi utamanya, tangani kesalahan yang mungkin terjadi, dan dokumentasikan hasil pengujiannya.',
            ],

            'company-network-security-simulation' => [
                'career' => 'Teknik Informatika',
                'area' => 'Jaringan dan Sistem Komputer',
                'original' => 'Sebuah kantor kecil memiliki beberapa divisi',
                'brief' => 'Sebuah kantor kecil memiliki beberapa divisi yang membutuhkan akses jaringan berbeda. Rancang jaringan yang memungkinkan setiap divisi bekerja dengan lancar tanpa mengabaikan keamanan. Tentukan topologi, pembagian alamat IP, layanan jaringan, serta aturan akses. Gunakan simulasi untuk membuktikan konektivitas dan menunjukkan bagaimana jaringan menangani akses yang tidak diizinkan.',
            ],

            'ai-predictive-project' => [
                'career' => 'Teknik Informatika',
                'area' => 'Artificial Intelligence',
                'original' => 'Gunakan dataset gambar yang legal digunakan',
                'brief' => 'Pilih masalah sederhana yang dapat diselesaikan menggunakan pengenalan gambar, misalnya membedakan beberapa jenis objek. Siapkan dataset yang boleh digunakan, periksa kualitas datanya, lalu bagi data untuk pelatihan dan pengujian. Bangun model machine learning, evaluasi hasilnya, dan jelaskan contoh prediksi yang benar maupun salah. Berikan alasan mengapa model tersebut sesuai untuk kasus yang dipilih.',
            ],

            'mini-computer-architecture-design' => [
                'career' => 'Sistem Komputer',
                'area' => 'Arsitektur dan Organisasi Komputer',
                'original' => 'Rancang sebuah sistem komputer sederhana',
                'brief' => 'Rancang sistem komputer sederhana yang menerima masukan, memprosesnya, lalu menghasilkan keluaran. Kamu dapat memilih contoh seperti penghitung otomatis atau pengendali akses ruangan. Buat diagram kerja sistem, tentukan komponen yang diperlukan, serta jelaskan hubungan antara logika digital dan mikrokontroler. Tunjukkan cara kerja rancangan melalui simulasi atau prototipe.',
            ],

            'smart-iot-system' => [
                'career' => 'Sistem Komputer',
                'area' => 'Embedded System dan Internet of Things',
                'original' => 'Buat prototipe Smart Home atau Smart Office',
                'brief' => 'Buat sistem sederhana untuk membantu pekerjaan di rumah atau kantor menggunakan sensor dan aktuator. Contohnya, lampu yang menyala berdasarkan kondisi ruangan atau alat pemantau suhu. Tentukan data yang dibaca sensor, aturan kerja perangkat, dan tindakan yang harus dilakukan sistem. Tampilkan hasil pembacaan dan uji respons perangkat pada beberapa kondisi.',
            ],

            'secure-network-design' => [
                'career' => 'Sistem Komputer',
                'area' => 'Jaringan dan Keamanan Komputer',
                'original' => 'Buat rancangan jaringan untuk organisasi kecil',
                'brief' => 'Sebuah organisasi membutuhkan jaringan yang mudah dikelola sekaligus aman. Rancang jaringan untuk beberapa kelompok pengguna dengan kebutuhan akses berbeda. Tentukan perangkat, pembagian jaringan, layanan, dan aturan keamanan yang diperlukan. Peragakan cara administrator memantau atau mengatur jaringan, kemudian uji apakah aturan akses bekerja sesuai rancangan.',
            ],

            'employee-organizational-assessment' => [
                'career' => 'Psikologi',
                'area' => 'Psikologi Industri dan Organisasi',
                'original' => 'Gunakan kasus organisasi fiktif',
                'brief' => 'Sebuah perusahaan mengalami penurunan semangat kerja dan meningkatnya konflik antaranggota tim. Gunakan kasus tersebut sebagai bahan analisis psikologi organisasi. Identifikasi kemungkinan faktor yang memengaruhi perilaku karyawan, tentukan informasi tambahan yang perlu dikumpulkan, dan susun rekomendasi perbaikan. Jelaskan batasan analisis agar kesimpulan tidak dianggap sebagai diagnosis psikologis terhadap individu.',
            ],

            'counseling-case-simulation' => [
                'career' => 'Psikologi',
                'area' => 'Konseling',
                'original' => 'Gunakan kasus fiktif non-darurat',
                'brief' => 'Seorang mahasiswa fiktif merasa kesulitan mengatur kegiatan kuliah dan berkomunikasi dengan teman kelompoknya. Buat simulasi percakapan konseling yang membantu mahasiswa tersebut menjelaskan masalahnya. Tunjukkan penggunaan pertanyaan terbuka, mendengarkan aktif, refleksi perasaan, dan komunikasi yang tidak menghakimi. Setelah simulasi, evaluasi cara konselor merespons tanpa memberikan diagnosis atau memaksakan keputusan.',
            ],

            'mini-psychological-research' => [
                'career' => 'Psikologi',
                'area' => 'Penelitian Psikologi',
                'original' => 'Pilih topik psikologi nonklinis',
                'brief' => 'Lakukan penelitian sederhana mengenai perilaku atau pengalaman manusia dalam kehidupan sehari-hari, misalnya kebiasaan belajar mahasiswa. Tentukan pertanyaan penelitian, pilih metode yang sesuai, dan buat instrumen pengumpulan data. Gunakan responden yang bersedia atau data simulasi yang diberi keterangan jelas. Analisis hasilnya dan tuliskan kesimpulan berdasarkan data sambil menjaga privasi partisipan.',
            ],

            'crisis-communication-simulation' => [
                'career' => 'Ilmu Komunikasi',
                'area' => 'Public Relations',
                'original' => 'Gunakan kasus perusahaan fiktif',
                'brief' => 'Sebuah perusahaan fiktif mendapat keluhan publik setelah layanannya mengalami gangguan. Kamu diminta membantu tim hubungan masyarakat menyusun respons awal. Tentukan informasi yang sudah terverifikasi, pihak yang perlu diberi penjelasan, dan pesan utama yang akan disampaikan. Buat pernyataan resmi serta rencana komunikasi melalui media yang sesuai. Pastikan informasi tidak menyesatkan dan tidak menyalahkan pihak lain tanpa bukti.',
            ],

            'news-reporting-project' => [
                'career' => 'Ilmu Komunikasi',
                'area' => 'Jurnalistik',
                'original' => 'Pilih topik yang dapat diliput',
                'brief' => 'Pilih peristiwa atau kegiatan yang layak dijadikan berita, misalnya kegiatan kampus atau masyarakat. Tentukan informasi yang dibutuhkan, cari narasumber yang relevan, dan lakukan wawancara dengan persetujuan mereka. Periksa kebenaran informasi sebelum menulis berita. Susun laporan yang jelas, lengkap, dan membedakan fakta dari pendapat. Cantumkan sumber yang digunakan.',
            ],

            'digital-content-campaign' => [
                'career' => 'Ilmu Komunikasi',
                'area' => 'Digital Media',
                'original' => 'Pilih brand, organisasi, atau kampanye fiktif',
                'brief' => 'Sebuah organisasi ingin memperkenalkan programnya kepada mahasiswa melalui media sosial. Tentukan sasaran audiens, pesan utama, dan bentuk konten yang paling sesuai. Buat rencana unggahan, beberapa materi visual, serta video pendek yang saling mendukung. Jelaskan alasan pemilihan format dan cara menilai apakah pesan berhasil menjangkau audiens.',
            ],
        ];
    }
}
