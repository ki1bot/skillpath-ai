<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Skill;
use App\Support\AcademicAssessmentCatalog;
use Illuminate\Database\Seeder;
use RuntimeException;

class AcademicAssessmentSupplementalQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            'Sistem Informasi' => [
                'si-sql-data-processing' => $this->question(
                    'Sebuah tabel transaksi menyimpan banyak transaksi dari pelanggan yang sama. Ekspresi SQL yang paling tepat untuk menghitung jumlah pelanggan unik adalah?',
                    'COUNT(DISTINCT customer_id)',
                    'COUNT(*)',
                    'SUM(customer_id)',
                    'ORDER BY customer_id',
                ),
                'si-database-management' => $this->question(
                    'Kolom email pada tabel pengguna tidak boleh memiliki nilai yang sama untuk dua akun berbeda. Constraint database yang paling tepat adalah?',
                    'UNIQUE constraint',
                    'DEFAULT constraint',
                    'CHECK yang selalu bernilai benar',
                    'INDEX biasa tanpa aturan unik',
                ),
                'si-ui-design' => $this->question(
                    'Pengguna salah mengisi format email pada formulir. Respons antarmuka yang paling membantu adalah?',
                    'Menampilkan pesan kesalahan yang spesifik di dekat field email',
                    'Menghapus seluruh isi formulir tanpa penjelasan',
                    'Menyembunyikan field email',
                    'Mengubah warna seluruh halaman tanpa pesan',
                ),
            ],
            'Manajemen' => [
                'man-branding' => $this->question(
                    'Sebuah perusahaan ingin logo, tipografi, warna, dan gaya komunikasinya digunakan secara konsisten oleh semua tim. Dokumen yang paling tepat digunakan adalah?',
                    'Brand guideline',
                    'Laporan arus kas',
                    'Daftar inventaris',
                    'Job description',
                ),
                'man-financial-planning' => $this->question(
                    'Pendapatan aktual perusahaan lebih rendah dari proyeksi sementara pengeluaran tetap sama. Langkah financial planning yang paling tepat adalah?',
                    'Memperbarui proyeksi arus kas dan menyesuaikan rencana pengeluaran',
                    'Mengabaikan perubahan karena anggaran sudah dibuat',
                    'Menghapus seluruh catatan transaksi',
                    'Menambah pengeluaran tanpa menghitung kemampuan kas',
                ),
                'man-recruitment-selection' => $this->question(
                    'Perusahaan ingin membandingkan kandidat secara lebih konsisten dan mengurangi penilaian berdasarkan kesan pribadi pewawancara. Pendekatan yang paling tepat adalah?',
                    'Menggunakan wawancara terstruktur dengan pertanyaan dan rubrik yang sama',
                    'Memberikan pertanyaan berbeda tanpa kriteria penilaian',
                    'Memilih kandidat hanya berdasarkan intuisi pewawancara',
                    'Mengabaikan kompetensi yang dibutuhkan posisi',
                ),
            ],
            'Teknik Informatika' => [
                'ti-algorithms-data-structures' => $this->question(
                    'Binary search dapat digunakan secara benar dan efisien ketika?',
                    'Data sudah terurut berdasarkan nilai yang dicari',
                    'Data harus selalu berbentuk linked list acak',
                    'Semua elemen harus memiliki nilai yang sama',
                    'Data harus dihapus sebelum pencarian',
                ),
                'ti-computer-networks' => $this->question(
                    'Ketika pengguna mengetik nama domain tetapi aplikasi membutuhkan alamat IP server tujuan, layanan jaringan yang bertugas melakukan pemetaan tersebut adalah?',
                    'DNS',
                    'DHCP',
                    'FTP',
                    'SSH',
                ),
                'ti-machine-learning' => $this->question(
                    'Model memiliki akurasi sangat tinggi pada data training tetapi jauh lebih rendah pada data validation. Kondisi tersebut paling menunjukkan?',
                    'Overfitting',
                    'Normalisasi database',
                    'Packet loss',
                    'Deadlock pada operating system',
                ),
            ],
            'Sistem Komputer' => [
                'sk-computer-architecture' => $this->question(
                    'Pada arsitektur komputer, tujuan utama cache memory yang berada dekat dengan prosesor adalah?',
                    'Mengurangi waktu rata-rata akses terhadap data dan instruksi yang sering digunakan',
                    'Menggantikan seluruh fungsi penyimpanan permanen',
                    'Menghapus kebutuhan terhadap register',
                    'Mengubah sinyal digital menjadi analog',
                ),
                'sk-embedded-systems' => $this->question(
                    'Sebuah embedded system harus dapat pulih ketika program utama berhenti merespons. Mekanisme yang paling tepat digunakan adalah?',
                    'Watchdog timer',
                    'CSS media query',
                    'Database trigger',
                    'DNS resolver',
                ),
                'sk-computer-networks' => $this->question(
                    'Sebuah komputer akan mengirim paket ke jaringan yang berbeda dari subnet lokalnya. Perangkat atau alamat yang digunakan sebagai tujuan awal paket tersebut adalah?',
                    'Default gateway',
                    'Loopback address',
                    'Broadcast lokal sebagai tujuan akhir',
                    'Alamat MAC komputer itu sendiri',
                ),
            ],
            'Psikologi' => [
                'psi-employee-behavior' => $this->question(
                    'Perusahaan menemukan peningkatan ketidakhadiran dan keinginan karyawan untuk keluar. Data tambahan yang paling relevan untuk memahami perilaku tersebut adalah?',
                    'Kepuasan kerja dan faktor lingkungan kerja karyawan',
                    'Warna logo perusahaan',
                    'Jenis sistem operasi komputer kantor',
                    'Resolusi monitor yang digunakan',
                ),
                'psi-counseling-skills' => $this->question(
                    'Dalam sesi konseling, konselor mengulangi inti pernyataan klien dengan kalimat sendiri untuk memastikan pemahaman. Teknik tersebut merupakan contoh?',
                    'Paraphrasing dalam active listening',
                    'Memberikan diagnosis tanpa penggalian informasi',
                    'Mengalihkan pembicaraan ke pengalaman konselor',
                    'Menghentikan klien sebelum selesai berbicara',
                ),
                'psi-research-methodology' => $this->question(
                    'Dalam eksperimen, peserta dibagi secara acak ke kelompok perlakuan dan kontrol. Tujuan utama random assignment adalah?',
                    'Mengurangi pengaruh perbedaan awal antar kelompok terhadap hasil penelitian',
                    'Menjamin semua peserta memberikan jawaban yang sama',
                    'Menghilangkan kebutuhan untuk menganalisis data',
                    'Mengubah penelitian kuantitatif menjadi kualitatif',
                ),
            ],
            'Ilmu Komunikasi' => [
                'ikom-media-relations' => $this->question(
                    'Organisasi memiliki berita yang relevan untuk industri teknologi. Strategi media relations yang paling tepat adalah?',
                    'Mengirim pitch yang relevan kepada jurnalis atau media yang memang meliput topik tersebut',
                    'Mengirim pesan yang sama ke seluruh kontak tanpa melihat bidang liputannya',
                    'Menghindari seluruh komunikasi dengan media',
                    'Menghapus informasi utama dari materi publikasi',
                ),
                'ikom-news-writing' => $this->question(
                    'Dalam penulisan berita dengan struktur inverted pyramid, informasi yang paling penting ditempatkan?',
                    'Pada bagian awal berita',
                    'Hanya pada paragraf terakhir',
                    'Di luar naskah berita',
                    'Secara acak tanpa prioritas informasi',
                ),
                'ikom-content-creation' => $this->question(
                    'Konten digital dibuat untuk mendorong pengguna mendaftar ke sebuah acara. Elemen yang paling penting agar tujuan tindakan terlihat jelas adalah?',
                    'Call to action yang sesuai dengan tujuan pendaftaran',
                    'Menambah dekorasi tanpa hubungan dengan tujuan',
                    'Menghilangkan informasi cara mendaftar',
                    'Menggunakan sebanyak mungkin pesan yang saling berbeda',
                ),
            ],
        ];

        $assessments = Assessment::query()
            ->whereIn(
                'study_program',
                array_keys($definitions),
            )
            ->where(
                'is_active',
                true,
            )
            ->get()
            ->keyBy('study_program');

        $skills = Skill::query()
            ->get()
            ->keyBy('slug');

        foreach (
            $definitions as $studyProgram => $questionsBySkill
        ) {
            $assessment = $assessments->get(
                $studyProgram,
            );

            if (! $assessment) {
                continue;
            }

            $expectedSkillSlugs = AcademicAssessmentCatalog::supplementalSkillSlugs(
                $studyProgram,
            );

            if (
                array_keys($questionsBySkill)
                !== $expectedSkillSlugs
            ) {
                throw new RuntimeException(
                    'Definisi soal cadangan '.$studyProgram.' tidak sesuai dengan katalog Assesment.',
                );
            }

            foreach (
                $questionsBySkill as $skillSlug => $definition
            ) {
                $skill = $skills->get(
                    $skillSlug,
                );

                if (! $skill) {
                    throw new RuntimeException(
                        'Skill '.$skillSlug.' tidak ditemukan.',
                    );
                }

                $prompt = $definition[
                    'prompt'
                ];

                $options = $definition[
                    'options'
                ];

                $question = $assessment
                    ->questions()
                    ->where(
                        'skill_id',
                        $skill->id,
                    )
                    ->where(
                        'prompt',
                        $prompt,
                    )
                    ->first();

                if (! $question) {
                    $currentCount = $assessment
                        ->questions()
                        ->where(
                            'skill_id',
                            $skill->id,
                        )
                        ->count();

                    $capacity = AcademicAssessmentCatalog::questionCapacityForSkill(
                        $studyProgram,
                        $skillSlug,
                    );

                    if ($currentCount >= $capacity) {
                        throw new RuntimeException(
                            'Slot soal tambahan untuk skill '.$skillSlug.' sudah terisi oleh soal lain.',
                        );
                    }
                }

                $shift = abs(
                    crc32(
                        $skillSlug
                            .'|'
                            .$prompt,
                    ),
                ) % 4;

                $attributes = [
                    'skill_id' => $skill->id,
                    'question_type' => 'multiple_choice',
                    'prompt' => $prompt,
                    'practical_instructions' => null,
                    'evidence_required' => false,
                    'options' => [
                        'A' => $this->optionForPosition(
                            $options,
                            $shift,
                        ),
                        'B' => $this->optionForPosition(
                            $options,
                            $shift + 1,
                        ),
                        'C' => $this->optionForPosition(
                            $options,
                            $shift + 2,
                        ),
                        'D' => $this->optionForPosition(
                            $options,
                            $shift + 3,
                        ),
                    ],
                    'correct_answer' => $this->answerForIndex(
                        (4 - $shift) % 4,
                    ),
                    'explanation' => 'Jawaban dinilai berdasarkan pemahaman konsep pada skill '.$skill->name.'.',
                    'difficulty' => $skill->difficulty,
                ];

                if ($question) {
                    $question->update(
                        $attributes,
                    );
                } else {
                    $assessment
                        ->questions()
                        ->create(
                            $attributes,
                        );
                }
            }

            $assessment->update([
                'description' => 'Kerjakan 25 pertanyaan acak yang mewakili 9 kemampuan inti jurusan '
                    .$studyProgram
                    .'. Lima pertanyaan lain dari bank soal disiapkan sebagai soal cadangan untuk setiap sesi Assesment.',
            ]);

            $questionCount = $assessment
                ->questions()
                ->count();

            if (
                $questionCount
                !== AcademicAssessmentCatalog::QUESTION_POOL_SIZE
            ) {
                throw new RuntimeException(
                    'Bank soal '.$studyProgram.' harus memiliki tepat '
                        .AcademicAssessmentCatalog::QUESTION_POOL_SIZE
                        .' soal, tetapi ditemukan '
                        .$questionCount
                        .'.',
                );
            }

            foreach (
                AcademicAssessmentCatalog::skillSlugs(
                    $studyProgram,
                ) as $skillSlug
            ) {
                $skill = $skills->get(
                    $skillSlug,
                );

                if (! $skill) {
                    throw new RuntimeException(
                        'Skill '.$skillSlug.' tidak ditemukan.',
                    );
                }

                $expectedCount = AcademicAssessmentCatalog::questionCapacityForSkill(
                    $studyProgram,
                    $skillSlug,
                );

                $actualCount = $assessment
                    ->questions()
                    ->where(
                        'skill_id',
                        $skill->id,
                    )
                    ->count();

                if ($actualCount !== $expectedCount) {
                    throw new RuntimeException(
                        'Jumlah soal skill '.$skillSlug.' tidak sesuai dengan kapasitas bank soal.',
                    );
                }
            }
        }
    }

    /**
     * @return array{
     *     prompt: string,
     *     options: array{
     *         0: string,
     *         1: string,
     *         2: string,
     *         3: string
     *     }
     * }
     */
    private function question(
        string $prompt,
        string $correctOption,
        string $wrongOptionOne,
        string $wrongOptionTwo,
        string $wrongOptionThree,
    ): array {
        return [
            'prompt' => $prompt,
            'options' => [
                $correctOption,
                $wrongOptionOne,
                $wrongOptionTwo,
                $wrongOptionThree,
            ],
        ];
    }

    /**
     * @param  array{0: string, 1: string, 2: string, 3: string}  $options
     */
    private function optionForPosition(
        array $options,
        int $position,
    ): string {
        return $options[
            $position % 4
        ];
    }

    private function answerForIndex(
        int $index,
    ): string {
        return match ($index) {
            1 => 'B',
            2 => 'C',
            3 => 'D',
            default => 'A',
        };
    }
}
