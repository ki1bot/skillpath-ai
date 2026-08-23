<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\Career;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class AcademicAssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $sets = [
            'Sistem Informasi' => [
                'si-sql-data-processing' => [
                    'Sebuah tabel transaksi memiliki kolom customer_id dan total. Anda ingin menghitung total belanja setiap customer. Operasi SQL yang paling tepat adalah?',
                    [
                        'GROUP BY customer_id disertai SUM(total)',
                        'ORDER BY customer_id tanpa agregasi',
                        'DELETE berdasarkan customer_id',
                        'ALTER TABLE untuk setiap customer',
                    ],
                    'A',
                ],
                'si-spreadsheet-data-analysis' => [
                    'Anda memiliki ribuan baris penjualan dan ingin merangkum total per kategori tanpa membuat rumus satu per satu. Fitur spreadsheet yang paling tepat adalah?',
                    [
                        'Conditional Formatting',
                        'Pivot Table',
                        'Data Validation',
                        'Freeze Panes',
                    ],
                    'B',
                ],
                'si-business-intelligence-data-visualization' => [
                    'Dashboard manajemen ingin membandingkan pencapaian penjualan aktual terhadap target bulanan. Elemen yang paling relevan adalah?',
                    [
                        'KPI dengan nilai aktual, target, dan selisih',
                        'Daftar seluruh transaksi mentah',
                        'Paragraf panjang tanpa angka',
                        'Tabel tanpa periode waktu',
                    ],
                    'A',
                ],
                'si-database-management' => [
                    'Dalam database relasional, foreign key terutama digunakan untuk?',
                    [
                        'Menghubungkan data antar tabel dan menjaga integritas referensial',
                        'Mengubah seluruh kolom menjadi teks',
                        'Menghapus kebutuhan primary key',
                        'Membuat tampilan antarmuka',
                    ],
                    'A',
                ],
                'si-web-development' => [
                    'Frontend perlu mengambil daftar produk dari backend tanpa memuat ulang seluruh halaman. Pendekatan yang paling tepat adalah?',
                    [
                        'Memanggil endpoint API melalui HTTP dari frontend',
                        'Menulis data produk langsung di CSS',
                        'Mengganti database dengan file gambar',
                        'Menjalankan query SQL langsung dari browser pengguna',
                    ],
                    'A',
                ],
                'si-system-analysis-design' => [
                    'Sebelum merancang sistem baru, analis menemukan proses bisnis tiap divisi berbeda. Langkah awal yang paling tepat adalah?',
                    [
                        'Menggali kebutuhan dan memetakan proses yang berjalan',
                        'Langsung menentukan framework',
                        'Langsung membuat database production',
                        'Menghapus proses lama tanpa analisis',
                    ],
                    'A',
                ],
                'si-ui-design' => [
                    'Sebuah tombol utama dan tombol sekunder terlihat sama kuat sehingga pengguna bingung memilih aksi. Prinsip UI yang perlu diperbaiki adalah?',
                    [
                        'Hierarki visual',
                        'Normalisasi database',
                        'Routing jaringan',
                        'Version control',
                    ],
                    'A',
                ],
                'si-wireframing-prototyping' => [
                    'Tujuan utama membuat wireframe sebelum desain visual detail adalah?',
                    [
                        'Memvalidasi struktur, prioritas konten, dan alur interaksi',
                        'Menentukan password database',
                        'Mengoptimalkan query SQL',
                        'Mengukur bandwidth jaringan',
                    ],
                    'A',
                ],
                'si-user-research' => [
                    'Tim ingin mengetahui mengapa pengguna sering gagal menyelesaikan proses checkout. Metode yang paling langsung untuk memahami masalah penggunaan adalah?',
                    [
                        'Usability testing disertai observasi pengguna',
                        'Mengganti warna logo tanpa data',
                        'Menambah fitur tanpa riset',
                        'Menghapus halaman bantuan',
                    ],
                    'A',
                ],
            ],
            'Manajemen' => [
                'man-branding' => [
                    'Sebuah merek ingin dikenal sebagai produk premium yang sederhana dan tahan lama. Pernyataan ini terutama berkaitan dengan?',
                    [
                        'Brand positioning',
                        'Payroll',
                        'Inventory turnover',
                        'Recruitment funnel',
                    ],
                    'A',
                ],
                'man-digital-marketing' => [
                    'Kampanye iklan digital bertujuan menghasilkan pendaftaran. Metrik yang paling langsung untuk mengevaluasi hasil tujuan tersebut adalah?',
                    [
                        'Conversion rate pendaftaran',
                        'Jumlah warna pada banner',
                        'Ukuran logo',
                        'Jumlah halaman dokumen internal',
                    ],
                    'A',
                ],
                'man-market-research' => [
                    'Perusahaan ingin mengetahui alasan pelanggan berpindah ke kompetitor. Data yang paling relevan untuk dikumpulkan adalah?',
                    [
                        'Wawancara atau survei pelanggan yang berpindah',
                        'Daftar warna kantor',
                        'Jumlah perangkat karyawan',
                        'Nama file laporan lama',
                    ],
                    'A',
                ],
                'man-financial-planning' => [
                    'Dalam financial planning, proyeksi arus kas terutama digunakan untuk?',
                    [
                        'Memperkirakan kemampuan memenuhi kebutuhan kas pada periode mendatang',
                        'Menentukan warna identitas merek',
                        'Menyusun struktur organisasi',
                        'Menilai kualitas wawancara kandidat',
                    ],
                    'A',
                ],
                'man-financial-analysis' => [
                    'Rasio current ratio terutama membantu menilai?',
                    [
                        'Likuiditas jangka pendek perusahaan',
                        'Efektivitas desain logo',
                        'Kualitas rekrutmen',
                        'Jumlah impresi media sosial',
                    ],
                    'A',
                ],
                'man-investment-management' => [
                    'Diversifikasi portofolio dilakukan terutama untuk?',
                    [
                        'Mengurangi risiko spesifik dengan menyebar investasi',
                        'Menjamin semua investasi pasti untung',
                        'Menghilangkan seluruh risiko pasar',
                        'Meningkatkan biaya transaksi tanpa tujuan',
                    ],
                    'A',
                ],
                'man-recruitment-selection' => [
                    'Agar seleksi kandidat lebih konsisten, perusahaan sebaiknya?',
                    [
                        'Menggunakan kriteria kompetensi dan metode penilaian yang terstruktur',
                        'Mengandalkan intuisi pewawancara saja',
                        'Memilih berdasarkan urutan kedatangan',
                        'Mengabaikan kebutuhan jabatan',
                    ],
                    'A',
                ],
                'man-performance-management' => [
                    'Sasaran kinerja yang baik seharusnya?',
                    [
                        'Spesifik, terukur, relevan, dan memiliki batas waktu',
                        'Berubah setiap hari tanpa alasan',
                        'Tidak memiliki indikator',
                        'Hanya diketahui oleh atasan',
                    ],
                    'A',
                ],
                'man-talent-management' => [
                    'Succession planning dalam talent management bertujuan untuk?',
                    [
                        'Menyiapkan kandidat internal bagi peran penting di masa depan',
                        'Menghapus seluruh program pengembangan',
                        'Mengganti seluruh karyawan setiap tahun',
                        'Menghindari evaluasi kompetensi',
                    ],
                    'A',
                ],
            ],
            'Teknik Informatika' => [
                'ti-algorithms-data-structures' => [
                    'Anda membutuhkan struktur data dengan pola Last In First Out. Struktur yang paling tepat adalah?',
                    [
                        'Queue',
                        'Stack',
                        'Graph',
                        'Heap',
                    ],
                    'B',
                ],
                'ti-object-oriented-programming' => [
                    'Menyembunyikan detail internal objek dan menyediakan akses melalui antarmuka yang terkontrol disebut?',
                    [
                        'Encapsulation',
                        'Recursion',
                        'Compilation',
                        'Serialization',
                    ],
                    'A',
                ],
                'ti-software-engineering' => [
                    'Requirement berubah setelah pengembangan berjalan. Praktik yang paling tepat adalah?',
                    [
                        'Menganalisis dampak perubahan lalu memperbarui requirement dan rencana implementasi',
                        'Mengabaikan perubahan tanpa komunikasi',
                        'Menghapus seluruh source code',
                        'Menerapkan perubahan langsung ke production tanpa pengujian',
                    ],
                    'A',
                ],
                'ti-computer-networks' => [
                    'Perangkat ingin mengirim paket ke jaringan yang berbeda dari subnet lokalnya. Tujuan awal paket biasanya diarahkan ke?',
                    [
                        'Default gateway',
                        'Loopback address',
                        'Broadcast aplikasi',
                        'Port USB',
                    ],
                    'A',
                ],
                'ti-operating-systems' => [
                    'Virtual memory memungkinkan sistem operasi untuk?',
                    [
                        'Menggunakan ruang penyimpanan sebagai perluasan logis memori utama ketika diperlukan',
                        'Menghilangkan kebutuhan CPU',
                        'Menjalankan jaringan tanpa protokol',
                        'Menghapus seluruh proses latar belakang',
                    ],
                    'A',
                ],
                'ti-cybersecurity' => [
                    'Prinsip least privilege berarti?',
                    [
                        'Memberikan hak akses minimum yang diperlukan untuk menjalankan tugas',
                        'Memberikan akses administrator ke semua pengguna',
                        'Menyimpan password dalam teks biasa',
                        'Menonaktifkan seluruh logging',
                    ],
                    'A',
                ],
                'ti-machine-learning' => [
                    'Model sangat baik pada data training tetapi buruk pada data baru. Kondisi ini paling mungkin disebut?',
                    [
                        'Underfitting',
                        'Overfitting',
                        'Normalization',
                        'Clustering',
                    ],
                    'B',
                ],
                'ti-data-science' => [
                    'Sebelum membangun model prediktif, langkah penting ketika dataset memiliki banyak nilai kosong adalah?',
                    [
                        'Menganalisis pola missing value lalu menentukan penanganan yang sesuai',
                        'Mengabaikan seluruh kualitas data',
                        'Mengganti semua nilai dengan angka acak',
                        'Menghapus target analisis',
                    ],
                    'A',
                ],
                'ti-computer-vision' => [
                    'Dalam klasifikasi citra, data augmentation umumnya digunakan untuk?',
                    [
                        'Menambah variasi data training secara terkontrol',
                        'Menghapus seluruh label',
                        'Mengubah tugas menjadi routing jaringan',
                        'Menggantikan proses evaluasi model',
                    ],
                    'A',
                ],
            ],
            'Psikologi' => [
                'psi-employee-behavior' => [
                    'Karyawan mengalami penurunan motivasi setelah merasa usahanya tidak pernah diakui. Faktor organisasi yang paling relevan untuk dievaluasi adalah?',
                    [
                        'Sistem penghargaan dan umpan balik',
                        'Resolusi layar',
                        'Topologi jaringan',
                        'Struktur tabel database',
                    ],
                    'A',
                ],
                'psi-organizational-development' => [
                    'Sebelum menjalankan intervensi organizational development, langkah yang paling tepat adalah?',
                    [
                        'Melakukan diagnosis kebutuhan organisasi berdasarkan data',
                        'Langsung mengganti seluruh struktur',
                        'Menyalin program organisasi lain tanpa analisis',
                        'Menghindari keterlibatan stakeholder',
                    ],
                    'A',
                ],
                'psi-psychological-assessment' => [
                    'Alat asesmen psikologis sebaiknya dipilih terutama berdasarkan?',
                    [
                        'Tujuan asesmen, bukti validitas, reliabilitas, dan kesesuaian penggunaannya',
                        'Popularitas di media sosial',
                        'Warna instrumen',
                        'Kemudahan mendapatkan skor tinggi',
                    ],
                    'A',
                ],
                'psi-counseling-skills' => [
                    'Klien mengatakan, “Saya merasa gagal dan tidak tahu harus mulai dari mana.” Respons konselor yang paling menunjukkan active listening adalah?',
                    [
                        '“Kamu merasa kewalahan dan sulit melihat langkah berikutnya, benar begitu?”',
                        '“Jangan dipikirkan, semua orang juga begitu.”',
                        '“Kamu harus langsung mengambil keputusan.”',
                        '“Masalah itu sebenarnya sederhana.”',
                    ],
                    'A',
                ],
                'psi-interpersonal-communication' => [
                    'Ketika terjadi salah paham dalam komunikasi interpersonal, langkah yang paling konstruktif adalah?',
                    [
                        'Mengklarifikasi makna pesan dan mendengarkan perspektif pihak lain',
                        'Menaikkan nada bicara',
                        'Menghindari pembicaraan selamanya',
                        'Menganggap niat pihak lain tanpa bertanya',
                    ],
                    'A',
                ],
                'psi-emotional-intelligence' => [
                    'Seseorang menyadari dirinya sedang marah sebelum merespons konflik. Kemampuan ini terutama menunjukkan?',
                    [
                        'Self-awareness',
                        'Data visualization',
                        'Recruitment',
                        'Network routing',
                    ],
                    'A',
                ],
                'psi-research-methodology' => [
                    'Peneliti ingin menguji hubungan antara kualitas tidur dan tingkat stres pada mahasiswa. Desain awal yang paling sesuai untuk melihat hubungan tanpa manipulasi adalah?',
                    [
                        'Studi korelasional',
                        'Eksperimen dengan manipulasi wajib',
                        'Studi tanpa variabel terukur',
                        'Observasi tanpa pencatatan data',
                    ],
                    'A',
                ],
                'psi-interview-observation' => [
                    'Agar hasil observasi perilaku lebih konsisten antar observer, peneliti sebaiknya?',
                    [
                        'Menggunakan definisi operasional dan pedoman coding yang jelas',
                        'Membiarkan setiap observer membuat definisi sendiri',
                        'Tidak mencatat waktu observasi',
                        'Mengubah kategori setelah semua data selesai tanpa alasan',
                    ],
                    'A',
                ],
                'psi-survey-data-analysis' => [
                    'Sebelum menyimpulkan hasil survei, peneliti perlu memeriksa apakah sampel?',
                    [
                        'Relevan dengan populasi sasaran dan proses pengambilannya dapat dijelaskan',
                        'Memiliki jawaban yang semuanya sama',
                        'Hanya berisi responden yang mudah dihubungi tanpa pertimbangan',
                        'Selalu berjumlah tepat 100 orang',
                    ],
                    'A',
                ],
            ],
            'Ilmu Komunikasi' => [
                'ikom-media-relations' => [
                    'Saat mengirim press release kepada media, informasi yang paling penting adalah?',
                    [
                        'Fakta yang bernilai berita, jelas, terverifikasi, dan memiliki narahubung',
                        'Pesan promosi tanpa data',
                        'Dokumen tanpa judul',
                        'Informasi yang belum dikonfirmasi',
                    ],
                    'A',
                ],
                'ikom-corporate-communication' => [
                    'Perusahaan mengumumkan perubahan kebijakan besar. Agar komunikasi korporat konsisten, langkah yang paling tepat adalah?',
                    [
                        'Menyelaraskan pesan utama dan informasi untuk stakeholder yang berbeda',
                        'Membiarkan tiap kanal memberi versi fakta berbeda',
                        'Menunda penjelasan tanpa batas',
                        'Menghapus seluruh saluran komunikasi',
                    ],
                    'A',
                ],
                'ikom-crisis-communication' => [
                    'Pada awal krisis, organisasi belum memiliki seluruh fakta. Respons komunikasi yang paling tepat adalah?',
                    [
                        'Menyampaikan fakta yang sudah terverifikasi, mengakui hal yang masih diselidiki, dan memberi pembaruan berkala',
                        'Mengarang detail agar terlihat cepat',
                        'Tidak merespons sama sekali',
                        'Menyalahkan pihak lain tanpa bukti',
                    ],
                    'A',
                ],
                'ikom-news-writing' => [
                    'Dalam penulisan berita hard news, informasi terpenting umumnya ditempatkan?',
                    [
                        'Di bagian awal melalui struktur piramida terbalik',
                        'Hanya di paragraf terakhir',
                        'Di caption tanpa isi berita',
                        'Secara acak agar mengejutkan pembaca',
                    ],
                    'A',
                ],
                'ikom-journalistic-interview' => [
                    'Untuk memperoleh jawaban mendalam dari narasumber, pewawancara sebaiknya lebih banyak menggunakan?',
                    [
                        'Pertanyaan terbuka yang spesifik dan relevan',
                        'Pertanyaan yang sudah mengandung jawaban',
                        'Pertanyaan di luar topik',
                        'Pertanyaan yang hanya dapat dijawab ya atau tidak untuk semua hal',
                    ],
                    'A',
                ],
                'ikom-news-reporting' => [
                    'Sebelum mempublikasikan klaim penting dari satu sumber, reporter sebaiknya?',
                    [
                        'Melakukan verifikasi dan mencari konfirmasi atau bukti pendukung',
                        'Langsung menerbitkan karena sumber terdengar yakin',
                        'Menghapus konteks',
                        'Mengubah kutipan agar lebih menarik',
                    ],
                    'A',
                ],
                'ikom-content-creation' => [
                    'Sebelum membuat konten digital, langkah yang paling menentukan arah pesan adalah?',
                    [
                        'Menentukan tujuan komunikasi dan audiens',
                        'Memilih font terlebih dahulu tanpa tujuan',
                        'Mengunggah konten acak',
                        'Menyalin konten kompetitor sepenuhnya',
                    ],
                    'A',
                ],
                'ikom-social-media-management' => [
                    'Akun organisasi ingin meningkatkan interaksi yang bermakna. Evaluasi yang lebih berguna dibanding sekadar jumlah follower adalah?',
                    [
                        'Engagement rate dan kualitas respons audiens',
                        'Ukuran file logo',
                        'Jumlah folder internal',
                        'Kecepatan CPU admin',
                    ],
                    'A',
                ],
                'ikom-video-production' => [
                    'Dokumen yang membantu merencanakan urutan visual dan pengambilan gambar sebelum produksi video adalah?',
                    [
                        'Storyboard atau shot list',
                        'Balance sheet',
                        'ERD',
                        'Routing table',
                    ],
                    'A',
                ],
            ],
        ];

        Assessment::query()
            ->whereNull('study_program')
            ->update(['is_active' => false]);

        Assessment::query()
            ->whereNotNull('study_program')
            ->whereNotIn('study_program', array_keys($sets))
            ->update(['is_active' => false]);

        $skills = Skill::query()
            ->get()
            ->keyBy('slug');

        $careers = Career::query()
            ->where('is_active', true)
            ->get();

        foreach ($careers as $career) {
            foreach ($sets as $studyProgram => $questions) {
                $assessment = Assessment::updateOrCreate(
                    [
                        'career_id' => $career->id,
                        'study_program' => $studyProgram,
                    ],
                    [
                        'title' => 'Assesment Awal '.$studyProgram,
                        'description' => 'Jawab 9 pertanyaan yang mewakili 3 bidang utama pada jurusan '.$studyProgram.'. Hasilnya digunakan untuk memperbarui profil kemampuan awal Anda.',
                        'duration_minutes' => 18,
                        'is_active' => true,
                    ],
                );

                foreach ($questions as $skillSlug => [$prompt, $options, $answer]) {
                    $skill = $skills->get($skillSlug);

                    if (! $skill) {
                        continue;
                    }

                    $skillSlug = (string) $skillSlug;
                    $answer = (string) $answer;

                    $optionA = (string) $options[0];
                    $optionB = (string) $options[1];
                    $optionC = (string) $options[2];
                    $optionD = (string) $options[3];

                    $shift = abs(crc32($skillSlug)) % 4;

                    $correctIndex = $this->correctAnswerIndex(
                        $answer,
                    );

                    $preparedAnswer = $this->answerForIndex(
                        ($correctIndex - $shift + 4) % 4,
                    );

                    AssessmentQuestion::updateOrCreate(
                        [
                            'assessment_id' => $assessment->id,
                            'skill_id' => $skill->id,
                        ],
                        [
                            'question_type' => 'multiple_choice',
                            'prompt' => $prompt,
                            'practical_instructions' => null,
                            'evidence_required' => false,
                            'options' => [
                                'A' => $this->optionForPosition(
                                    $optionA,
                                    $optionB,
                                    $optionC,
                                    $optionD,
                                    $shift,
                                ),
                                'B' => $this->optionForPosition(
                                    $optionA,
                                    $optionB,
                                    $optionC,
                                    $optionD,
                                    $shift + 1,
                                ),
                                'C' => $this->optionForPosition(
                                    $optionA,
                                    $optionB,
                                    $optionC,
                                    $optionD,
                                    $shift + 2,
                                ),
                                'D' => $this->optionForPosition(
                                    $optionA,
                                    $optionB,
                                    $optionC,
                                    $optionD,
                                    $shift + 3,
                                ),
                            ],
                            'correct_answer' => $preparedAnswer,
                            'explanation' => 'Jawaban dinilai berdasarkan pemahaman konsep dasar pada skill '.$skill->name.'.',
                            'difficulty' => $skill->difficulty,
                        ],
                    );
                }
            }
        }
    }

    private function correctAnswerIndex(
        string $answer,
    ): int {
        return match ($answer) {
            'B' => 1,
            'C' => 2,
            'D' => 3,
            default => 0,
        };
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

    private function optionForPosition(
        string $optionA,
        string $optionB,
        string $optionC,
        string $optionD,
        int $position,
    ): string {
        return match ($position % 4) {
            0 => $optionA,
            1 => $optionB,
            2 => $optionC,
            default => $optionD,
        };
    }
}
