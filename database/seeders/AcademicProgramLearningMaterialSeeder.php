<?php

namespace Database\Seeders;

use App\Models\AssessmentQuestion;
use App\Models\LearningMaterial;
use App\Models\Skill;
use App\Support\AcademicProgramCatalog;
use Illuminate\Database\Seeder;
use RuntimeException;

class AcademicProgramLearningMaterialSeeder extends Seeder
{
    private const PRACTICE_TASKS = [
        'si-sql-data-processing' => 'Buat database sederhana untuk kasus penjualan yang memiliki tabel pelanggan, produk, dan transaksi. Isi setiap tabel dengan beberapa data contoh. Setelah itu buat minimal lima query yang mencakup filter data, JOIN, agregasi, GROUP BY, dan pengurutan. Simpan script SQL serta tangkapan hasil setiap query agar admin dapat memeriksa apakah hasilnya sesuai dengan kebutuhan kasus.',
        'si-spreadsheet-data-analysis' => 'Buat spreadsheet berisi minimal 30 baris data transaksi atau penjualan. Rapikan data yang tidak konsisten, lalu gunakan beberapa rumus seperti SUM, AVERAGE, COUNTIF atau SUMIF, dan fungsi pencarian yang sesuai. Buat satu tabel ringkasan dan satu grafik yang membantu membaca hasil analisis. Sertakan penjelasan singkat mengenai dua temuan yang kamu dapatkan dari data tersebut.',
        'si-business-intelligence-data-visualization' => 'Gunakan satu dataset sederhana untuk membuat dashboard yang memiliki minimal tiga indikator utama dan dua jenis visualisasi. Pastikan setiap grafik mempunyai judul, label, dan tujuan yang jelas. Sertakan penjelasan mengenai alasan memilih visualisasi tersebut serta minimal tiga insight yang bisa diambil dari dashboard.',
        'si-database-management' => 'Rancang database untuk sistem peminjaman atau penjualan sederhana dengan minimal empat entitas. Buat ERD, tentukan primary key dan foreign key, lalu jelaskan hubungan antarentitas. Setelah itu buat struktur tabel dalam SQL dan tunjukkan bagaimana data dapat ditambah, diubah, dicari, dan dihapus dengan aman.',
        'si-web-development' => 'Buat aplikasi web sederhana yang memiliki satu fitur CRUD lengkap, misalnya pengelolaan daftar buku, tugas, produk, atau catatan. Aplikasi harus mempunyai form input, validasi data, daftar data, fungsi edit, dan fungsi hapus. Sertakan tangkapan layar hasil aplikasi serta penjelasan singkat mengenai alur request dari pengguna sampai data disimpan.',
        'si-system-analysis-design' => 'Pilih satu kasus sistem sederhana, misalnya peminjaman ruang, pemesanan makanan, atau pengelolaan inventaris. Tentukan aktor yang terlibat, tuliskan minimal lima kebutuhan fungsional dan tiga kebutuhan nonfungsional, lalu buat use case diagram atau activity diagram. Jelaskan bagaimana rancangan tersebut menjawab masalah pada proses lama.',
        'si-ui-design' => 'Buat rancangan antarmuka untuk dua halaman aplikasi, misalnya dashboard dan halaman detail. Tentukan hierarki informasi, tipografi, warna, ukuran tombol, dan pola navigasi yang konsisten. Sertakan hasil desain serta penjelasan singkat mengenai alasan penempatan elemen utama dan bagaimana desain membantu pengguna menyelesaikan tugasnya.',
        'si-wireframing-prototyping' => 'Buat wireframe untuk minimal tiga layar yang saling berhubungan, kemudian ubah menjadi prototype yang dapat diklik. Gunakan satu alur pengguna yang jelas, misalnya login sampai membuat data baru. Sertakan gambar wireframe, link prototype jika tersedia, dan penjelasan mengenai perubahan yang dilakukan dari wireframe awal ke prototype.',
        'si-user-research' => 'Tentukan satu masalah penggunaan aplikasi yang ingin dipahami. Susun minimal lima pertanyaan wawancara, lakukan wawancara singkat kepada minimal tiga responden secara sukarela, lalu rangkum temuan tanpa mencantumkan data pribadi yang tidak diperlukan. Kelompokkan masalah yang muncul dan tuliskan minimal tiga rekomendasi perbaikan produk.',

        'man-branding' => 'Pilih satu produk atau usaha dan buat rancangan identitas merek yang mencakup target konsumen, positioning, nilai utama merek, kepribadian merek, serta pesan utama. Buat satu contoh penerapan identitas tersebut pada media promosi dan jelaskan mengapa pilihan visual serta pesannya sesuai dengan target konsumen.',
        'man-digital-marketing' => 'Susun rencana kampanye digital selama tujuh hari untuk satu produk atau layanan. Tentukan tujuan kampanye, target audiens, minimal dua kanal digital, jenis konten setiap hari, call to action, dan indikator keberhasilan yang akan diukur. Jelaskan alasan pemilihan kanal dan bagaimana hasil kampanye nantinya dievaluasi.',
        'man-market-research' => 'Tentukan satu pertanyaan riset pasar untuk produk atau layanan tertentu. Buat kuesioner singkat, kumpulkan minimal sepuluh jawaban secara sukarela atau gunakan data simulasi yang dijelaskan dengan jelas, lalu rangkum hasilnya dalam tabel atau grafik. Tuliskan minimal tiga kesimpulan dan satu keputusan bisnis yang dapat diambil dari temuan tersebut.',
        'man-financial-planning' => 'Buat simulasi rencana keuangan bulanan untuk seseorang dengan pendapatan dan pengeluaran yang realistis. Kelompokkan kebutuhan rutin, tabungan, dana darurat, dan tujuan keuangan. Hitung proporsi masing-masing pos dan buat skenario jika pendapatan turun atau ada pengeluaran tidak terduga. Jelaskan perubahan yang perlu dilakukan agar kondisi keuangan tetap sehat.',
        'man-financial-analysis' => 'Gunakan contoh laporan keuangan sederhana yang memiliki data pendapatan, beban, aset, dan kewajiban. Hitung minimal tiga rasio yang relevan, misalnya margin laba, current ratio, dan debt ratio. Jelaskan arti setiap hasil perhitungan serta simpulkan kondisi keuangan berdasarkan angka yang diperoleh.',
        'man-investment-management' => 'Bandingkan minimal tiga instrumen investasi berdasarkan potensi imbal hasil, risiko, likuiditas, dan jangka waktu. Buat contoh profil investor dan susun alokasi portofolio yang sesuai dengan profil tersebut. Jelaskan alasan pembagian dana serta risiko utama yang perlu dipahami sebelum keputusan investasi dilakukan.',
        'man-recruitment-selection' => 'Buat rancangan proses rekrutmen untuk satu posisi pekerjaan. Susun job description, kriteria wajib dan tambahan, metode penyaringan kandidat, serta minimal lima pertanyaan wawancara. Buat rubrik penilaian sederhana agar keputusan pemilihan kandidat dapat dibandingkan berdasarkan kriteria yang sama.',
        'man-performance-management' => 'Pilih satu posisi pekerjaan dan tentukan minimal tiga indikator kinerja yang dapat diukur. Tetapkan target untuk masing-masing indikator, cara pengumpulan data, serta jadwal evaluasinya. Buat contoh hasil evaluasi seorang karyawan dan tuliskan umpan balik yang spesifik, seimbang, dan dapat ditindaklanjuti.',
        'man-talent-management' => 'Buat contoh pemetaan lima karyawan fiktif berdasarkan kinerja dan potensi. Tentukan siapa yang membutuhkan pengembangan, siapa yang siap menerima tanggung jawab lebih besar, dan siapa yang memerlukan pendampingan. Susun satu rencana pengembangan singkat untuk masing-masing kategori dan jelaskan alasan keputusan tersebut.',

        'ti-algorithms-data-structures' => 'Buat program yang menyelesaikan satu masalah menggunakan minimal dua struktur data atau algoritma yang berbeda. Contohnya pencarian data, pengurutan, stack, queue, atau pengelolaan daftar. Uji program dengan beberapa input, tampilkan hasilnya, lalu bandingkan kompleksitas waktu dan alasan memilih pendekatan yang paling sesuai.',
        'ti-object-oriented-programming' => 'Buat program kecil seperti sistem perpustakaan, inventaris, atau pemesanan dengan beberapa class yang memiliki tanggung jawab berbeda. Terapkan constructor, encapsulation, inheritance atau interface jika relevan, serta polymorphism pada bagian yang memang membutuhkan. Sertakan contoh penggunaan program dan jelaskan hubungan antarclass.',
        'ti-software-engineering' => 'Gunakan skenario sebuah aplikasi yang sudah dikembangkan lalu mengalami perubahan requirement. Tuliskan requirement awal dan requirement baru, analisis bagian sistem yang terdampak, tentukan perubahan desain atau implementasi yang diperlukan, lalu buat daftar pengujian yang harus dilakukan sebelum perubahan dinyatakan siap. Jelaskan juga risiko jika perubahan langsung diterapkan tanpa analisis.',
        'ti-computer-networks' => 'Rancang jaringan untuk sebuah kantor kecil yang memiliki minimal empat bagian. Buat topologi jaringan, tentukan pembagian subnet dan alamat IP setiap bagian, lalu buat tabel berisi network address, range host, dan broadcast address. Jelaskan alur komunikasi antarbagian dan cara memastikan perangkat dapat saling terhubung dengan benar.',
        'ti-operating-systems' => 'Buat simulasi sederhana penjadwalan proses menggunakan minimal dua algoritma, misalnya FCFS dan Round Robin. Gunakan sekumpulan proses dengan arrival time dan burst time yang berbeda. Hitung waiting time dan turnaround time, kemudian bandingkan hasil kedua algoritma dan jelaskan kondisi ketika salah satunya lebih sesuai digunakan.',
        'ti-cybersecurity' => 'Pilih satu fitur aplikasi seperti login atau pengunggahan file, kemudian buat threat model sederhana. Identifikasi minimal lima ancaman yang masuk akal, dampaknya, dan tindakan mitigasi yang aman. Sertakan rancangan konfigurasi atau validasi yang dapat diterapkan tanpa melakukan eksploitasi terhadap sistem milik pihak lain.',
        'ti-machine-learning' => 'Gunakan dataset kecil untuk membuat model klasifikasi sederhana. Pisahkan data training dan testing, lakukan preprocessing yang diperlukan, latih satu model, lalu tampilkan metrik seperti accuracy, precision, recall, atau confusion matrix. Jelaskan hasil model serta minimal dua hal yang dapat menyebabkan performanya kurang baik.',
        'ti-data-science' => 'Pilih dataset yang memiliki beberapa kolom numerik atau kategorikal. Lakukan pemeriksaan data kosong dan duplikat, bersihkan data jika diperlukan, kemudian buat minimal tiga analisis atau visualisasi. Tuliskan tiga insight yang benar-benar didukung oleh data dan bedakan antara fakta dari dataset dengan dugaan yang masih perlu diuji.',
        'ti-computer-vision' => 'Buat percobaan computer vision sederhana menggunakan beberapa gambar, misalnya deteksi tepi, segmentasi dasar, atau klasifikasi gambar dengan model yang tersedia. Simpan contoh input dan output, jelaskan tahapan preprocessing yang digunakan, lalu bandingkan hasil pada gambar yang mudah dan gambar yang lebih sulit.',

        'sk-computer-architecture' => 'Buat diagram sederhana yang menunjukkan hubungan CPU, register, cache, memori utama, dan perangkat input-output. Pilih satu instruksi sederhana dan jelaskan alurnya dari fetch, decode, sampai execute. Jelaskan juga bagaimana register dan cache membantu mengurangi waktu akses dibandingkan jika CPU selalu mengambil data langsung dari memori utama.',
        'sk-digital-logic' => 'Buat satu fungsi logika dengan minimal tiga input. Susun truth table, tuliskan persamaan Boolean, sederhanakan persamaan tersebut menggunakan metode yang sudah dipelajari, lalu gambar rangkaian gerbang logikanya. Bandingkan jumlah gerbang sebelum dan sesudah penyederhanaan.',
        'sk-microprocessor-microcontroller' => 'Buat rangkaian sederhana menggunakan mikrokontroler atau simulator seperti Arduino yang menghubungkan minimal satu input dan satu output. Contohnya tombol untuk mengendalikan LED. Sertakan diagram rangkaian, kode program, hasil pengujian, serta penjelasan singkat mengenai fungsi pin dan alur program.',
        'sk-embedded-systems' => 'Rancang sistem embedded sederhana yang membaca input sensor dan menentukan keluaran berdasarkan kondisi tertentu. Buat diagram blok, algoritma atau flowchart, serta contoh program atau pseudocode. Jelaskan bagaimana sistem menangani pembacaan sensor berulang dan kondisi ketika nilai sensor berada di luar batas yang ditentukan.',
        'sk-internet-of-things' => 'Rancang sistem IoT sederhana dari perangkat sensor sampai data tampil pada aplikasi atau dashboard. Buat diagram yang menunjukkan perangkat, koneksi jaringan, protokol atau API, penyimpanan data, dan aplikasi pengguna. Sertakan contoh format data yang dikirim dan jelaskan minimal tiga langkah keamanan yang perlu diterapkan.',
        'sk-sensor-actuator-integration' => 'Buat rancangan yang menghubungkan satu sensor dengan satu aktuator, misalnya sensor suhu dengan kipas atau sensor cahaya dengan lampu. Tentukan nilai ambang, buat flowchart kontrol, dan sertakan hasil simulasi atau pengujian. Jelaskan bagaimana perubahan nilai sensor memengaruhi aktuator dan bagaimana menghindari pembacaan yang tidak stabil.',
        'sk-computer-networks' => 'Buat rancangan jaringan kecil dengan beberapa subnet dan minimal satu perangkat penghubung antarjaringan. Tentukan alamat IP setiap subnet, gateway, serta perangkat yang digunakan. Jelaskan jalur paket dari satu host ke host di subnet lain dan sertakan hasil pengujian konektivitas dari simulasi atau konfigurasi yang dibuat.',
        'sk-network-administration' => 'Siapkan lingkungan Linux atau simulator jaringan untuk membuat contoh konfigurasi administrator. Atur pengguna, alamat jaringan, layanan dasar yang diperlukan, dan aturan akses yang sederhana. Dokumentasikan setiap konfigurasi penting serta cara memeriksa status layanan dan log ketika terjadi masalah.',
        'sk-network-security' => 'Buat hardening checklist untuk sebuah server jaringan sederhana. Checklist minimal mencakup akun pengguna, autentikasi, layanan yang tidak diperlukan, firewall, pembaruan sistem, backup, dan pemantauan log. Buat contoh aturan akses yang menerapkan prinsip least privilege dan jelaskan alasan setiap aturan.',

        'psi-employee-behavior' => 'Analisis satu kasus fiktif mengenai perilaku karyawan, misalnya motivasi menurun, konflik tim, atau perubahan beban kerja. Identifikasi faktor individu dan lingkungan yang mungkin memengaruhi perilaku tersebut. Tuliskan alternatif tindakan yang dapat dilakukan organisasi serta indikator yang digunakan untuk melihat apakah kondisi membaik.',
        'psi-organizational-development' => 'Gunakan contoh organisasi fiktif yang mengalami masalah seperti komunikasi buruk atau perubahan proses kerja. Buat diagnosis awal berdasarkan informasi yang tersedia, tentukan tujuan perubahan, pilih bentuk intervensi yang sesuai, dan susun cara mengevaluasi hasilnya. Jelaskan juga risiko jika perubahan dilakukan tanpa melibatkan pihak yang terdampak.',
        'psi-psychological-assessment' => 'Pilih dua metode asesmen psikologis yang berbeda dan bandingkan tujuan, jenis data, kelebihan, keterbatasan, serta konteks penggunaannya. Buat contoh kasus fiktif untuk menunjukkan metode mana yang lebih sesuai. Sertakan batasan etika dan jelaskan mengapa hasil asesmen tidak boleh ditafsirkan di luar kompetensi yang dimiliki.',
        'psi-counseling-skills' => 'Buat skenario percakapan konseling singkat dengan kasus fiktif. Tunjukkan penggunaan pertanyaan terbuka, active listening, paraphrasing, refleksi perasaan, dan rangkuman. Setelah percakapan, jelaskan bagian yang membantu membangun hubungan dan bagian yang harus dihindari agar konselor tidak menghakimi atau memaksakan keputusan.',
        'psi-interpersonal-communication' => 'Buat satu skenario konflik komunikasi antara dua orang. Tuliskan versi komunikasi yang memperburuk masalah, kemudian perbaiki dengan bahasa yang lebih asertif dan jelas. Jelaskan perubahan pada pilihan kata, cara menyampaikan kebutuhan, dan bentuk umpan balik yang membuat komunikasi lebih konstruktif.',
        'psi-emotional-intelligence' => 'Buat jurnal refleksi selama beberapa hari mengenai situasi, emosi yang muncul, respons yang diberikan, dan cara mengelolanya. Jangan mencantumkan informasi pribadi orang lain yang tidak diperlukan. Setelah selesai, rangkum pola yang terlihat dan tuliskan strategi regulasi emosi yang dapat digunakan pada situasi serupa.',
        'psi-research-methodology' => 'Tentukan satu topik penelitian psikologi. Buat rumusan masalah, tujuan penelitian, variabel atau fokus yang diteliti, hipotesis jika diperlukan, metode pengumpulan data, teknik sampling, serta rencana analisis. Jelaskan aspek etika yang harus diperhatikan sebelum data dikumpulkan.',
        'psi-interview-observation' => 'Buat pedoman wawancara dengan minimal enam pertanyaan dan satu lembar observasi sederhana untuk topik yang sama. Lakukan simulasi atau latihan dengan partisipan yang bersedia, lalu tuliskan hasil secara anonim. Pisahkan antara apa yang benar-benar diamati dengan interpretasi pribadi dan jelaskan potensi bias yang mungkin muncul.',
        'psi-survey-data-analysis' => 'Susun survei singkat dengan minimal delapan item untuk mengukur satu topik psikologi yang tidak bersifat diagnosis klinis. Gunakan data sukarela atau data simulasi, buat tabel distribusi jawaban, hitung statistik deskriptif yang sesuai, dan tampilkan minimal satu grafik. Tuliskan kesimpulan yang tidak melebihi informasi yang tersedia pada data.',

        'ikom-media-relations' => 'Pilih satu kegiatan organisasi dan buat paket media sederhana yang terdiri dari press release, daftar media yang relevan, dan pesan pendek untuk menawarkan informasi kepada jurnalis. Pastikan press release memiliki judul, lead, informasi utama, kutipan, dan kontak yang jelas. Jelaskan alasan pemilihan media yang dituju.',
        'ikom-corporate-communication' => 'Buat rencana komunikasi untuk sebuah perusahaan fiktif yang akan menjalankan kebijakan atau program baru. Petakan stakeholder internal dan eksternal, tentukan pesan utama untuk masing-masing kelompok, pilih kanal komunikasi, dan buat contoh satu materi komunikasi. Jelaskan bagaimana konsistensi pesan dijaga di seluruh kanal.',
        'ikom-crisis-communication' => 'Gunakan satu skenario krisis perusahaan fiktif. Buat holding statement, daftar lima pertanyaan yang kemungkinan muncul dari publik atau media beserta jawabannya, serta urutan tindakan komunikasi untuk beberapa jam pertama. Jelaskan informasi apa yang perlu dikonfirmasi sebelum diumumkan agar perusahaan tidak menyebarkan informasi yang belum pasti.',
        'ikom-news-writing' => 'Pilih satu peristiwa nyata yang dapat diverifikasi atau gunakan skenario yang dinyatakan sebagai simulasi. Susun unsur 5W+1H, buat headline dan lead, kemudian tulis berita sekitar 300 sampai 500 kata dengan struktur piramida terbalik. Cantumkan sumber informasi secara jelas dan bedakan fakta dengan opini.',
        'ikom-journalistic-interview' => 'Tentukan satu topik wawancara dan susun minimal delapan pertanyaan yang terdiri dari pertanyaan pembuka, pendalaman, dan klarifikasi. Lakukan wawancara simulasi atau wawancara dengan narasumber yang bersedia. Buat ringkasan atau transkrip bagian penting, pilih kutipan yang relevan, dan jelaskan alasan pemilihan kutipan tersebut.',
        'ikom-news-reporting' => 'Buat rencana peliputan untuk satu topik. Tentukan informasi yang perlu dikumpulkan, minimal tiga jenis sumber yang dapat digunakan untuk verifikasi, serta pertanyaan yang perlu dijawab sebelum berita ditulis. Setelah itu buat laporan berita dan jelaskan bagaimana setiap fakta utama diperiksa sebelum dimasukkan.',
        'ikom-content-creation' => 'Pilih satu tema dan target audiens, lalu buat content brief yang menjelaskan tujuan, pesan utama, gaya komunikasi, dan call to action. Buat minimal tiga contoh konten dalam format yang berbeda, misalnya carousel, caption, dan poster. Jelaskan bagaimana setiap format disesuaikan dengan kebiasaan audiens.',
        'ikom-social-media-management' => 'Buat kalender konten selama tujuh hari untuk satu brand atau organisasi. Tentukan tujuan setiap konten, format, waktu publikasi, caption singkat, call to action, dan indikator yang ingin diukur. Tambahkan pedoman sederhana untuk merespons komentar atau pertanyaan pengguna secara konsisten.',
        'ikom-video-production' => 'Buat konsep video berdurasi sekitar 60 sampai 90 detik. Susun tujuan video, target penonton, storyboard, shot list, dan kebutuhan audio. Produksi atau simulasikan videonya, lakukan penyuntingan dasar, lalu sertakan hasil akhir beserta penjelasan mengenai keputusan pengambilan gambar dan editing yang paling berpengaruh terhadap pesan video.',
    ];

    public function run(): void
    {
        $canonicalSlugs = AcademicProgramCatalog::allSkillSlugs();

        $legacySkillIds = Skill::query()
            ->where(
                function ($query) {
                    $query
                        ->where('slug', 'like', 'si-%')
                        ->orWhere('slug', 'like', 'man-%')
                        ->orWhere('slug', 'like', 'ti-%')
                        ->orWhere('slug', 'like', 'sk-%')
                        ->orWhere('slug', 'like', 'psi-%')
                        ->orWhere('slug', 'like', 'ikom-%');
                },
            )
            ->whereNotIn(
                'slug',
                $canonicalSlugs,
            )
            ->pluck('id');

        if ($legacySkillIds->isNotEmpty()) {
            LearningMaterial::query()
                ->whereIn(
                    'skill_id',
                    $legacySkillIds,
                )
                ->update([
                    'is_active' => false,
                ]);
        }

        $skills = Skill::query()
            ->whereIn(
                'slug',
                $canonicalSlugs,
            )
            ->get();

        if ($skills->count() !== count($canonicalSlugs)) {
            throw new RuntimeException(
                'Katalog skill akademik belum lengkap untuk membuat materi pembelajaran.',
            );
        }

        $canonicalMaterialSlugs = collect(
            $canonicalSlugs,
        )
            ->flatMap(
                fn (string $skillSlug) => [
                    'belajar-'.$skillSlug,
                    'penguatan-'.$skillSlug,
                ],
            )
            ->values()
            ->all();

        LearningMaterial::query()
            ->whereIn(
                'skill_id',
                $skills->pluck('id'),
            )
            ->whereNotIn(
                'slug',
                $canonicalMaterialSlugs,
            )
            ->update([
                'is_active' => false,
            ]);

        foreach ($skills as $skill) {
            $studyProgram = $this->studyProgramForSkill(
                $skill->slug,
            );

            $question = $this->assessmentQuestionForSkill(
                $skill,
                $studyProgram,
            );

            $quiz = $this->quizData(
                $skill,
                $question,
            );

            $practiceTask = $this->practiceTask(
                $skill->slug,
            );

            $core = LearningMaterial::updateOrCreate(
                [
                    'slug' => 'belajar-'.$skill->slug,
                ],
                [
                    'skill_id' => $skill->id,
                    'material_type' => 'core',
                    'reinforcement_for_material_id' => null,
                    'is_active' => true,
                    'title' => 'Memahami '.$skill->name,
                    'summary' => $skill->description,
                    'learning_objectives' => [
                        'Memahami konsep utama '.$skill->name,
                        'Menghubungkan konsep dengan situasi nyata',
                        'Menerapkan pemahaman melalui tugas yang dapat diperiksa',
                    ],
                    'difficulty' => $skill->difficulty,
                    'estimated_minutes' => $skill->difficulty === 'Dasar'
                        ? 90
                        : 120,
                    'resource_title' => 'Materi pembelajaran SkillPath',
                    'resource_url' => null,
                    'practice_task' => $practiceTask,
                    'quiz_question' => $quiz['question'],
                    'quiz_options' => $quiz['options'],
                    'quiz_answer' => $quiz['answer'],
                    'quiz_explanation' => $quiz['explanation'],
                ],
            );

            LearningMaterial::updateOrCreate(
                [
                    'slug' => 'penguatan-'.$skill->slug,
                ],
                [
                    'skill_id' => $skill->id,
                    'material_type' => 'reinforcement',
                    'reinforcement_for_material_id' => $core->id,
                    'is_active' => true,
                    'title' => 'Penguatan: '.$skill->name,
                    'summary' => 'Pelajari kembali bagian penting dari '.$skill->name.' dengan ruang lingkup yang lebih kecil sebelum mengirim perbaikan tugas.',
                    'learning_objectives' => [
                        'Mengulang bagian yang masih belum dipahami',
                        'Menemukan bagian tugas sebelumnya yang perlu diperbaiki',
                        'Mengerjakan kembali tugas dengan langkah yang lebih terarah',
                    ],
                    'difficulty' => $skill->difficulty,
                    'estimated_minutes' => 60,
                    'resource_title' => 'Materi penguatan SkillPath',
                    'resource_url' => null,
                    'practice_task' => $this->reinforcementTask(
                        $skill->name,
                        $practiceTask,
                    ),
                    'quiz_question' => $quiz['question'],
                    'quiz_options' => $quiz['options'],
                    'quiz_answer' => $quiz['answer'],
                    'quiz_explanation' => $quiz['explanation'],
                ],
            );
        }
    }

    private function practiceTask(
        string $skillSlug,
    ): string {
        $task = self::PRACTICE_TASKS[
            $skillSlug
        ] ?? null;

        if ($task === null) {
            throw new RuntimeException(
                'Tugas praktik belum tersedia untuk skill '.$skillSlug.'.',
            );
        }

        return $task;
    }

    private function reinforcementTask(
        string $skillName,
        string $practiceTask,
    ): string {
        return 'Periksa kembali tugas '.$skillName.' yang sebelumnya sudah kamu kerjakan. Fokus pada catatan yang diberikan admin, lalu buat versi perbaikan dengan ruang lingkup yang lebih kecil dan lebih rapi. '.$practiceTask.' Sertakan hasil perbaikan dan catatan singkat mengenai bagian apa yang kamu ubah dibandingkan pengumpulan sebelumnya.';
    }

    private function studyProgramForSkill(
        string $skillSlug,
    ): string {
        foreach (
            array_keys(
                AcademicProgramCatalog::programs(),
            ) as $studyProgram
        ) {
            if (
                in_array(
                    $skillSlug,
                    AcademicProgramCatalog::skillSlugs(
                        $studyProgram,
                    ),
                    true,
                )
            ) {
                return $studyProgram;
            }
        }

        throw new RuntimeException(
            'Skill '.$skillSlug.' tidak terhubung dengan jurusan akademik.',
        );
    }

    private function assessmentQuestionForSkill(
        Skill $skill,
        string $studyProgram,
    ): ?AssessmentQuestion {
        $program = AcademicProgramCatalog::program(
            $studyProgram,
        );

        if ($program === null) {
            throw new RuntimeException(
                'Jurusan '.$studyProgram.' tidak ditemukan pada katalog akademik.',
            );
        }

        return AssessmentQuestion::query()
            ->where(
                'skill_id',
                $skill->id,
            )
            ->whereHas(
                'assessment',
                fn ($query) => $query
                    ->where(
                        'is_active',
                        true,
                    )
                    ->where(
                        'study_program',
                        $studyProgram,
                    )
                    ->whereHas(
                        'career',
                        fn ($careerQuery) => $careerQuery
                            ->where(
                                'slug',
                                $program['slug'],
                            )
                            ->where(
                                'name',
                                $studyProgram,
                            )
                            ->where(
                                'is_active',
                                true,
                            ),
                    ),
            )
            ->orderBy('id')
            ->first();
    }

    /**
     * @return array{
     *     question: string,
     *     options: array<string, string>,
     *     answer: string,
     *     explanation: string
     * }
     */
    private function quizData(
        Skill $skill,
        ?AssessmentQuestion $question,
    ): array {
        if ($question) {
            $options = $this->questionOptions(
                $question,
            );

            if ($options !== null) {
                return [
                    'question' => $question->prompt,
                    'options' => $options,
                    'answer' => $question->correct_answer,
                    'explanation' => $question->explanation
                        ?? 'Jawaban dinilai berdasarkan pemahaman konsep '.$skill->name.'.',
                ];
            }
        }

        return [
            'question' => 'Pernyataan mana yang paling tepat menggambarkan cara mempelajari '.$skill->name.'?',
            'options' => [
                'A' => 'Memahami konsep utamanya, melihat penerapan, lalu mencoba latihan yang relevan.',
                'B' => 'Menghafal istilah tanpa memahami konteks atau penerapannya.',
                'C' => 'Mengabaikan konsep dasar dan langsung meniru hasil akhir.',
                'D' => 'Menghindari latihan dan evaluasi agar tidak menemukan kesalahan.',
            ],
            'answer' => 'A',
            'explanation' => 'Pembelajaran '.$skill->name.' perlu mencakup pemahaman konsep, konteks penerapan, dan latihan yang dapat dievaluasi.',
        ];
    }

    /**
     * @return array<string, string>|null
     */
    private function questionOptions(
        AssessmentQuestion $question,
    ): ?array {
        $rawOptions = $question->getAttribute(
            'options',
        );

        if (
            ! is_array($rawOptions)
            || $rawOptions === []
        ) {
            return null;
        }

        $options = [];

        foreach ($rawOptions as $key => $value) {
            if (! is_string($value)) {
                return null;
            }

            $options[
                (string) $key
            ] = $value;
        }

        return $options;
    }
}
