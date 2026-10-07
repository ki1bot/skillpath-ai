<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\PortfolioProject;
use App\Models\Skill;
use App\Support\AcademicProgramCatalog;
use App\Support\SkillPathScoringPolicy;
use Illuminate\Database\Seeder;
use RuntimeException;

class AcademicPortfolioProjectSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = $this->definitions();

        $programNames = array_values(
            array_unique(
                array_map(
                    fn (array $definition): string => $definition[
                        'career'
                    ],
                    $definitions,
                ),
            ),
        );

        $careers = Career::query()
            ->whereIn(
                'name',
                $programNames,
            )
            ->get()
            ->keyBy('name');

        $skills = Skill::query()
            ->whereIn(
                'slug',
                AcademicProgramCatalog::allSkillSlugs(),
            )
            ->get()
            ->keyBy('slug');

        $canonicalSlugs = [];

        foreach ($definitions as $definition) {
            $careerName = $definition['career'];
            $areaName = $definition['area'];
            $title = $definition['title'];

            $career = $careers->get(
                $careerName,
            );

            if (! $career) {
                throw new RuntimeException(
                    'Jurusan '.$careerName.' belum tersedia untuk proyek '.$title.'.',
                );
            }

            $project = PortfolioProject::updateOrCreate(
                [
                    'slug' => $definition['slug'],
                ],
                [
                    'career_id' => $career->id,
                    'title' => $title,
                    'summary' => $definition[
                        'summary'
                    ],
                    'problem_statement' => $definition[
                        'problem_statement'
                    ],
                    'difficulty' => 'Menengah',
                    'minimum_features' => $definition[
                        'minimum_features'
                    ],
                    'stretch_features' => $definition[
                        'stretch_features'
                    ],
                    'completion_criteria' => $definition[
                        'completion_criteria'
                    ],
                    'estimated_hours' => $definition[
                        'estimated_hours'
                    ],
                ],
            );

            $skillSlugs = AcademicProgramCatalog::areaSkillSlugs(
                $careerName,
                $areaName,
            );

            if (count($skillSlugs) !== 3) {
                throw new RuntimeException(
                    'Bidang '.$areaName.' pada jurusan '.$careerName.' harus memiliki tepat 3 skill.',
                );
            }

            $sync = [];

            foreach ($skillSlugs as $skillSlug) {
                $skill = $skills->get(
                    $skillSlug,
                );

                if (! $skill) {
                    throw new RuntimeException(
                        'Skill '.$skillSlug.' untuk proyek '.$title.' belum tersedia.',
                    );
                }

                $sync[$skill->id] = [
                    'required_level' => SkillPathScoringPolicy::FUNCTIONAL_LEVEL,
                    'weight' => SkillPathScoringPolicy::DEFAULT_PROJECT_WEIGHT,
                ];
            }

            $project
                ->skills()
                ->sync(
                    $sync,
                );

            $canonicalSlugs[] = $project->slug;
        }

        $academicCareerIds = $careers
            ->pluck('id')
            ->all();

        if ($academicCareerIds === []) {
            return;
        }

        $legacyProjects = PortfolioProject::query()
            ->whereIn(
                'career_id',
                $academicCareerIds,
            )
            ->whereNotIn(
                'slug',
                $canonicalSlugs,
            );

        if (
            (clone $legacyProjects)
                ->whereHas('userProjects')
                ->exists()
        ) {
            throw new RuntimeException(
                'Masih ada progres pengguna pada proyek lama. Pindahkan atau hapus progres tersebut sebelum membersihkan proyek lama.',
            );
        }

        $legacyProjects->delete();
    }

    private function definitions(): array
    {
        return [
            [
                'career' => 'Sistem Informasi',
                'area' => 'Analisis Data',
                'title' => 'Sales & Business Intelligence Dashboard',
                'slug' => 'sales-business-intelligence-dashboard',
                'summary' => 'Mengolah data penjualan mentah menjadi laporan dan dashboard yang dapat digunakan untuk membaca kondisi bisnis.',
                'problem_statement' => 'Sebuah perusahaan memiliki data transaksi penjualan yang belum tertata dan sulit dibaca oleh manajemen. Tugasmu adalah membersihkan data tersebut, menyusun query untuk menjawab kebutuhan bisnis, melakukan analisis menggunakan spreadsheet, lalu membuat dashboard Business Intelligence. Hasil akhir tidak cukup hanya berupa grafik; setiap visualisasi harus membantu menjawab pertanyaan bisnis yang jelas.',
                'estimated_hours' => 18,
                'minimum_features' => [
                    'Siapkan dataset transaksi yang memiliki informasi tanggal, produk, pelanggan, jumlah, harga, dan wilayah.',
                    'Bersihkan data duplikat, data kosong, format tanggal, serta nilai yang tidak konsisten dan dokumentasikan perubahan yang dilakukan.',
                    'Buat minimal lima query SQL yang mencakup filter, JOIN, agregasi, GROUP BY, dan pengurutan.',
                    'Buat analisis spreadsheet yang menghasilkan ringkasan penjualan, rata-rata transaksi, produk terlaris, dan perbandingan periode.',
                    'Bangun dashboard Business Intelligence dengan minimal tiga indikator utama dan tiga visualisasi yang relevan.',
                    'Tuliskan minimal lima insight serta tiga rekomendasi bisnis yang benar-benar didukung oleh hasil analisis.',
                ],
                'stretch_features' => [
                    'Tambahkan filter interaktif berdasarkan periode, wilayah, atau kategori produk.',
                    'Tambahkan perbandingan performa dengan periode sebelumnya.',
                ],
                'completion_criteria' => [
                    'Dataset awal dan hasil pembersihan tersedia dan dapat dibandingkan.',
                    'Script atau file query SQL dapat dijalankan dan hasilnya sesuai dengan kebutuhan analisis.',
                    'Spreadsheet memiliki rumus atau pengolahan yang dapat ditelusuri.',
                    'Dashboard memiliki label, satuan, judul, dan visualisasi yang mudah dibaca.',
                    'Kesimpulan tidak mengklaim hal yang tidak didukung oleh data.',
                ],
            ],
            [
                'career' => 'Sistem Informasi',
                'area' => 'Pengembangan Sistem',
                'title' => 'Build Mini Information System',
                'slug' => 'build-mini-information-system',
                'summary' => 'Membangun sistem informasi kecil dari analisis kebutuhan sampai aplikasi dapat digunakan dan datanya tersimpan dengan benar.',
                'problem_statement' => 'Pilih satu kasus yang cukup sederhana tetapi nyata, misalnya pengelolaan inventaris, peminjaman ruang, pencatatan penjualan, atau pengelolaan tugas. Mulai dari memahami masalah pengguna, buat rancangan sistem dan database, lalu bangun aplikasi web yang menyelesaikan proses utama. Admin akan menilai konsistensi antara kebutuhan, rancangan, database, antarmuka, dan fungsi aplikasi.',
                'estimated_hours' => 28,
                'minimum_features' => [
                    'Tuliskan masalah utama, aktor yang menggunakan sistem, minimal lima kebutuhan fungsional, dan tiga kebutuhan nonfungsional.',
                    'Buat use case, activity diagram, atau rancangan alur yang menunjukkan proses utama sistem.',
                    'Buat ERD dan database dengan primary key, foreign key, serta relasi yang sesuai.',
                    'Bangun antarmuka untuk menampilkan, menambah, mengubah, dan menghapus data utama.',
                    'Tambahkan validasi input dan penanganan kondisi ketika data tidak valid atau tidak ditemukan.',
                    'Lakukan pengujian terhadap alur utama dan dokumentasikan hasil pengujiannya.',
                ],
                'stretch_features' => [
                    'Tambahkan autentikasi dan pembagian hak akses sederhana.',
                    'Tambahkan pencarian, filter, atau pagination pada data utama.',
                ],
                'completion_criteria' => [
                    'Requirement, rancangan, dan implementasi membahas kasus yang sama.',
                    'Database dapat digunakan tanpa relasi yang rusak atau data utama yang tidak konsisten.',
                    'Fitur utama dapat digunakan dari awal sampai akhir.',
                    'Validasi mencegah input yang tidak sesuai kebutuhan sistem.',
                    'Folder pengumpulan berisi source code, dokumentasi, serta bukti aplikasi berjalan.',
                ],
            ],
            [
                'career' => 'Sistem Informasi',
                'area' => 'UI/UX',
                'title' => 'Redesign Digital Product',
                'slug' => 'redesign-digital-product',
                'summary' => 'Merancang ulang pengalaman pengguna berdasarkan masalah yang ditemukan melalui riset dan pengujian desain.',
                'problem_statement' => 'Pilih satu aplikasi atau layanan digital yang memiliki alur pengguna yang dapat diperbaiki. Jangan langsung membuat tampilan baru. Cari tahu lebih dulu masalah yang dialami pengguna, rangkum temuannya, kemudian buat user flow, wireframe, desain antarmuka, dan prototype. Setelah itu lakukan validasi sederhana untuk melihat apakah rancangan baru benar-benar lebih mudah digunakan.',
                'estimated_hours' => 20,
                'minimum_features' => [
                    'Tentukan satu alur pengguna yang menjadi fokus redesign dan jelaskan masalah awalnya.',
                    'Lakukan riset sederhana kepada minimal tiga responden sukarela dan hindari mengumpulkan data pribadi yang tidak diperlukan.',
                    'Rangkum temuan menjadi masalah pengguna dan prioritas perbaikan.',
                    'Buat user flow serta wireframe minimal tiga layar yang saling berhubungan.',
                    'Buat high-fidelity UI dan prototype yang dapat diklik untuk alur utama.',
                    'Lakukan validasi kepada pengguna atau melalui usability walkthrough dan dokumentasikan perubahan setelah evaluasi.',
                ],
                'stretch_features' => [
                    'Buat komponen design system sederhana.',
                    'Tambahkan pengujian aksesibilitas dasar seperti kontras dan ukuran area sentuh.',
                ],
                'completion_criteria' => [
                    'Setiap perubahan desain mempunyai alasan yang berasal dari temuan pengguna.',
                    'User flow, wireframe, dan prototype menunjukkan alur yang konsisten.',
                    'Elemen visual memiliki hierarki, spacing, dan gaya yang konsisten.',
                    'Prototype dapat digunakan untuk menjalankan skenario utama.',
                    'Hasil validasi dan perubahan setelah pengujian didokumentasikan.',
                ],
            ],
            [
                'career' => 'Manajemen',
                'area' => 'Marketing',
                'title' => 'Digital Marketing Campaign',
                'slug' => 'digital-marketing-campaign',
                'summary' => 'Menyusun kampanye digital yang menghubungkan riset pasar, identitas merek, strategi konten, dan pengukuran hasil.',
                'problem_statement' => 'Pilih produk atau layanan yang jelas. Sebelum membuat konten, tentukan siapa konsumennya dan masalah apa yang ingin diselesaikan. Gunakan riset pasar sederhana sebagai dasar, kemudian susun positioning, pesan merek, kanal pemasaran, kalender kampanye, dan cara mengukur keberhasilannya.',
                'estimated_hours' => 16,
                'minimum_features' => [
                    'Definisikan produk, tujuan kampanye, serta target audiens yang spesifik.',
                    'Lakukan market research sederhana menggunakan data sukarela, data publik, atau data simulasi yang diberi keterangan.',
                    'Susun positioning, nilai utama merek, tone of voice, dan pesan utama kampanye.',
                    'Pilih minimal dua kanal digital dan jelaskan alasan pemilihannya.',
                    'Buat kalender konten minimal tujuh hari lengkap dengan format, pesan, call to action, dan tujuan setiap konten.',
                    'Tentukan KPI dan buat contoh cara mengevaluasi hasil kampanye.',
                ],
                'stretch_features' => [
                    'Buat dua versi konten untuk simulasi A/B testing.',
                    'Tambahkan estimasi sederhana biaya dan hasil kampanye.',
                ],
                'completion_criteria' => [
                    'Target audiens dan positioning tidak terlalu umum.',
                    'Keputusan kampanye memiliki dasar dari hasil market research.',
                    'Branding konsisten pada seluruh contoh konten.',
                    'KPI dapat diukur dan relevan dengan tujuan kampanye.',
                    'Laporan akhir menjelaskan apa yang akan dipertahankan atau diperbaiki dari kampanye.',
                ],
            ],
            [
                'career' => 'Manajemen',
                'area' => 'Keuangan',
                'title' => 'Financial Health Analysis',
                'slug' => 'financial-health-analysis',
                'summary' => 'Menganalisis kondisi keuangan dan menyusun rencana perbaikan serta keputusan investasi yang dapat dipertanggungjawabkan.',
                'problem_statement' => 'Gunakan laporan keuangan sederhana dari perusahaan fiktif atau data yang memang boleh digunakan. Analisis kondisi saat ini, cari titik masalah, lalu buat rencana keuangan dan rekomendasi investasi yang sesuai dengan kemampuan serta risiko. Kesimpulan harus berasal dari perhitungan, bukan sekadar pendapat.',
                'estimated_hours' => 16,
                'minimum_features' => [
                    'Susun data pendapatan, beban, aset, kewajiban, dan arus kas dalam format yang mudah diperiksa.',
                    'Hitung minimal empat indikator atau rasio keuangan yang relevan.',
                    'Jelaskan apa arti setiap hasil perhitungan terhadap kondisi keuangan.',
                    'Buat rencana keuangan untuk minimal enam bulan berikutnya.',
                    'Bandingkan minimal tiga alternatif keputusan investasi berdasarkan risiko, likuiditas, dan jangka waktu.',
                    'Buat satu skenario ketika pendapatan turun atau pengeluaran meningkat dan jelaskan perubahan rencana yang diperlukan.',
                ],
                'stretch_features' => [
                    'Tambahkan analisis sensitivitas terhadap perubahan pendapatan atau biaya.',
                    'Buat dashboard ringkas untuk menampilkan indikator utama.',
                ],
                'completion_criteria' => [
                    'Semua angka dapat ditelusuri ke data atau asumsi yang dijelaskan.',
                    'Rasio dihitung dengan rumus yang tepat.',
                    'Rencana keuangan sesuai dengan hasil analisis.',
                    'Rekomendasi investasi menyebutkan risiko dan tidak menjanjikan keuntungan pasti.',
                    'Kesimpulan membedakan fakta perhitungan dan asumsi.',
                ],
            ],
            [
                'career' => 'Manajemen',
                'area' => 'Human Resources',
                'title' => 'Recruitment Strategy',
                'slug' => 'recruitment-strategy',
                'summary' => 'Menyusun proses HR yang menghubungkan rekrutmen, penilaian kinerja, dan pengembangan talenta.',
                'problem_statement' => 'Bayangkan sebuah perusahaan sedang menambah satu posisi penting. Buat proses HR yang tidak berhenti setelah kandidat diterima. Mulai dari kebutuhan jabatan, seleksi kandidat, indikator kinerja setelah bekerja, sampai rencana pengembangan talenta untuk beberapa bulan berikutnya.',
                'estimated_hours' => 14,
                'minimum_features' => [
                    'Buat job analysis dan job description untuk satu posisi.',
                    'Tentukan kriteria wajib, kriteria tambahan, dan sumber kandidat.',
                    'Susun tahapan seleksi serta rubrik penilaian kandidat.',
                    'Buat minimal enam pertanyaan wawancara yang berhubungan langsung dengan kompetensi posisi.',
                    'Tentukan minimal empat KPI untuk mengevaluasi kinerja setelah kandidat diterima.',
                    'Susun rencana pengembangan talenta selama enam bulan berdasarkan contoh hasil evaluasi.',
                ],
                'stretch_features' => [
                    'Buat template onboarding 30 hari pertama.',
                    'Tambahkan succession plan sederhana untuk posisi tersebut.',
                ],
                'completion_criteria' => [
                    'Kriteria kandidat sesuai dengan tanggung jawab pekerjaan.',
                    'Rubrik membuat kandidat dapat dibandingkan secara konsisten.',
                    'KPI dapat diukur dan berada dalam kendali pekerjaan yang dinilai.',
                    'Rencana pengembangan berhubungan dengan hasil evaluasi.',
                    'Dokumen tidak menggunakan atribut pribadi yang tidak relevan sebagai dasar seleksi.',
                ],
            ],
            [
                'career' => 'Teknik Informatika',
                'area' => 'Pemrograman dan Rekayasa Perangkat Lunak',
                'title' => 'Software Development Project',
                'slug' => 'software-development-project',
                'summary' => 'Membangun aplikasi kecil dengan struktur program yang jelas, penggunaan OOP, algoritma yang sesuai, serta proses pengujian.',
                'problem_statement' => 'Buat aplikasi yang mempunyai masalah jelas untuk diselesaikan, misalnya pengelolaan inventaris, peminjaman, pengingat tugas, atau pencatatan transaksi. Fokus penilaian bukan jumlah fitur, tetapi bagaimana requirement diterjemahkan menjadi struktur data, class, fungsi, pengujian, dan dokumentasi yang masuk akal.',
                'estimated_hours' => 30,
                'minimum_features' => [
                    'Tuliskan requirement utama serta batasan sistem sebelum mulai membuat kode.',
                    'Gunakan struktur data dan algoritma yang sesuai untuk minimal satu proses penting.',
                    'Gunakan beberapa class dengan tanggung jawab yang jelas dan terapkan konsep OOP secara relevan.',
                    'Bangun fitur utama yang dapat menerima input, memproses data, menyimpan atau mengelola data, lalu menampilkan hasil.',
                    'Tambahkan validasi dan penanganan error pada input atau operasi penting.',
                    'Buat pengujian atau skenario uji untuk fitur utama dan dokumentasikan cara menjalankan aplikasi.',
                ],
                'stretch_features' => [
                    'Tambahkan automated test untuk fungsi atau service utama.',
                    'Tambahkan persistence menggunakan database jika proyek awal belum menggunakannya.',
                ],
                'completion_criteria' => [
                    'Kode dapat dijalankan dengan langkah yang tertulis di dokumentasi.',
                    'Struktur program tidak menempatkan seluruh logika dalam satu fungsi atau class tanpa alasan.',
                    'Algoritma dan struktur data yang dipilih sesuai dengan kebutuhan.',
                    'Fitur utama berhasil melewati skenario uji yang didokumentasikan.',
                    'Folder berisi source code, README, hasil pengujian, dan bukti aplikasi berjalan.',
                ],
            ],
            [
                'career' => 'Teknik Informatika',
                'area' => 'Jaringan dan Sistem Komputer',
                'title' => 'Company Network & Security Simulation',
                'slug' => 'company-network-security-simulation',
                'summary' => 'Merancang jaringan kantor, sistem operasi layanan, dan kontrol keamanan kemudian membuktikannya melalui simulasi.',
                'problem_statement' => 'Sebuah kantor kecil memiliki beberapa divisi yang membutuhkan jaringan terpisah tetapi tetap dapat mengakses layanan tertentu. Buat rancangan jaringan, pembagian alamat, konfigurasi layanan, serta kontrol keamanan. Gunakan simulator atau lab milik sendiri; jangan melakukan pengujian terhadap sistem pihak lain.',
                'estimated_hours' => 24,
                'minimum_features' => [
                    'Buat topologi dengan minimal tiga segmen atau divisi jaringan.',
                    'Hitung subnet dan dokumentasikan network address, host range, gateway, serta broadcast address.',
                    'Konfigurasikan routing agar komunikasi yang memang diperlukan dapat berjalan.',
                    'Tentukan sistem operasi dan minimal satu layanan jaringan yang digunakan.',
                    'Terapkan firewall atau access control untuk membatasi trafik yang tidak diperlukan.',
                    'Lakukan pengujian konektivitas dan keamanan dasar lalu dokumentasikan hasilnya.',
                ],
                'stretch_features' => [
                    'Tambahkan segmentasi VLAN.',
                    'Tambahkan monitoring atau pencatatan log layanan.',
                ],
                'completion_criteria' => [
                    'Topologi dan tabel IP sesuai satu sama lain.',
                    'Perangkat yang diperbolehkan dapat berkomunikasi.',
                    'Trafik yang seharusnya dibatasi benar-benar ditolak.',
                    'Konfigurasi sistem operasi dan layanan didokumentasikan.',
                    'Seluruh pengujian dilakukan pada lingkungan yang dimiliki atau diizinkan.',
                ],
            ],
            [
                'career' => 'Teknik Informatika',
                'area' => 'Artificial Intelligence',
                'title' => 'AI Predictive Project',
                'slug' => 'ai-predictive-project',
                'summary' => 'Membangun pipeline AI dari eksplorasi data sampai evaluasi model dengan kasus yang melibatkan data visual.',
                'problem_statement' => 'Gunakan dataset gambar yang legal digunakan, misalnya klasifikasi objek sederhana. Lakukan eksplorasi data, preprocessing, pembagian data, training model, dan evaluasi. Hasil akhir harus menjelaskan mengapa model berhasil atau gagal pada contoh tertentu, bukan hanya menampilkan angka accuracy.',
                'estimated_hours' => 24,
                'minimum_features' => [
                    'Jelaskan dataset, jumlah kelas, distribusi data, dan tujuan model.',
                    'Lakukan eksplorasi data dan tampilkan contoh data dari setiap kelas.',
                    'Buat preprocessing gambar yang konsisten dan pisahkan data training serta testing.',
                    'Latih minimal satu model klasifikasi atau computer vision yang relevan.',
                    'Evaluasi menggunakan metrik yang sesuai seperti accuracy, precision, recall, F1-score, atau confusion matrix.',
                    'Analisis minimal lima prediksi benar dan lima prediksi salah untuk menjelaskan keterbatasan model.',
                ],
                'stretch_features' => [
                    'Bandingkan dua model atau dua konfigurasi training.',
                    'Buat interface sederhana untuk mencoba prediksi gambar baru.',
                ],
                'completion_criteria' => [
                    'Dataset dan lisensi atau sumbernya dijelaskan.',
                    'Data testing tidak digunakan sebagai data training.',
                    'Langkah preprocessing dapat direproduksi.',
                    'Metrik evaluasi ditampilkan dan dijelaskan.',
                    'Kesimpulan tidak menyatakan model lebih baik daripada bukti evaluasi yang tersedia.',
                ],
            ],
            [
                'career' => 'Sistem Komputer',
                'area' => 'Arsitektur dan Organisasi Komputer',
                'title' => 'Mini Computer Architecture Design',
                'slug' => 'mini-computer-architecture-design',
                'summary' => 'Merancang sistem komputer sederhana yang menghubungkan arsitektur komputer, logika digital, dan pengendali mikro.',
                'problem_statement' => 'Rancang sebuah sistem komputer sederhana untuk kebutuhan tertentu, misalnya sistem kontrol akses atau penghitung otomatis. Tunjukkan bagaimana input diproses oleh logika digital dan pengendali, bagaimana data disimpan atau dipindahkan, dan bagaimana output dihasilkan.',
                'estimated_hours' => 18,
                'minimum_features' => [
                    'Tentukan tujuan sistem, input, proses, dan output yang dibutuhkan.',
                    'Buat diagram blok arsitektur yang menunjukkan unit pemrosesan, memori, input, dan output.',
                    'Buat minimal satu fungsi logika digital lengkap dengan truth table dan persamaan Boolean.',
                    'Jelaskan alur instruksi atau data dari input sampai output.',
                    'Gunakan simulator atau rancangan microprocessor/microcontroller untuk menunjukkan bagian kontrol.',
                    'Dokumentasikan hasil pengujian terhadap beberapa kondisi input.',
                ],
                'stretch_features' => [
                    'Sederhanakan rangkaian logika menggunakan Karnaugh Map.',
                    'Tambahkan interrupt atau mekanisme input asynchronous pada simulasi.',
                ],
                'completion_criteria' => [
                    'Diagram arsitektur sesuai dengan fungsi sistem.',
                    'Truth table dan persamaan logika menghasilkan keluaran yang sama.',
                    'Peran unit pemrosesan, memori, serta I/O dijelaskan.',
                    'Simulasi atau rancangan kontrol dapat menunjukkan alur kerja.',
                    'Hasil pengujian membahas kondisi normal dan minimal satu kondisi batas.',
                ],
            ],
            [
                'career' => 'Sistem Komputer',
                'area' => 'Embedded System dan Internet of Things',
                'title' => 'Smart IoT System',
                'slug' => 'smart-iot-system',
                'summary' => 'Membangun sistem IoT yang membaca sensor, mengambil keputusan, mengendalikan aktuator, dan mengirim data.',
                'problem_statement' => 'Buat prototipe Smart Home atau Smart Office dengan minimal satu sensor dan satu aktuator. Sistem harus mempunyai logika lokal, koneksi IoT, serta cara melihat data. Simulasi diperbolehkan bila perangkat fisik tidak tersedia.',
                'estimated_hours' => 24,
                'minimum_features' => [
                    'Tentukan masalah dan kondisi yang ingin dipantau atau dikendalikan.',
                    'Buat diagram blok yang menunjukkan sensor, microcontroller, jaringan, layanan IoT, dan aktuator.',
                    'Baca nilai sensor secara periodik dan tangani nilai yang tidak masuk akal.',
                    'Kendalikan aktuator berdasarkan aturan atau nilai ambang yang jelas.',
                    'Kirim data melalui protokol atau API dan tampilkan status pada dashboard atau aplikasi sederhana.',
                    'Uji beberapa skenario termasuk kondisi ketika koneksi atau pembacaan sensor bermasalah.',
                ],
                'stretch_features' => [
                    'Tambahkan kontrol manual dari dashboard.',
                    'Tambahkan penyimpanan histori data sensor.',
                ],
                'completion_criteria' => [
                    'Sensor dan aktuator mempunyai hubungan logika yang dapat dijelaskan.',
                    'Data yang dikirim mempunyai format yang konsisten.',
                    'Sistem tidak langsung gagal ketika satu pembacaan sensor tidak valid.',
                    'Dashboard atau output menunjukkan kondisi terbaru sistem.',
                    'Diagram, program, dan hasil pengujian tersedia dalam folder.',
                ],
            ],
            [
                'career' => 'Sistem Komputer',
                'area' => 'Jaringan dan Keamanan Komputer',
                'title' => 'Secure Network Design',
                'slug' => 'secure-network-design',
                'summary' => 'Merancang jaringan yang dapat diadministrasikan dan mempunyai kontrol keamanan yang sesuai dengan kebutuhan organisasi.',
                'problem_statement' => 'Buat rancangan jaringan untuk organisasi kecil dengan minimal tiga kelompok pengguna. Selain konektivitas, proyek harus memperlihatkan bagaimana administrator mengelola perangkat, akun, layanan, firewall, backup konfigurasi, dan log keamanan.',
                'estimated_hours' => 22,
                'minimum_features' => [
                    'Buat topologi serta pembagian subnet untuk minimal tiga kelompok pengguna.',
                    'Tentukan gateway, DNS, dan layanan jaringan yang dibutuhkan.',
                    'Buat contoh prosedur administrasi pengguna, perangkat, dan konfigurasi jaringan.',
                    'Terapkan aturan firewall atau ACL berdasarkan prinsip least privilege.',
                    'Buat hardening checklist untuk perangkat atau server utama.',
                    'Dokumentasikan pengujian konektivitas, aturan akses, backup konfigurasi, dan pemeriksaan log.',
                ],
                'stretch_features' => [
                    'Tambahkan segmentasi VLAN dan aturan komunikasi antar-VLAN.',
                    'Tambahkan simulasi pemulihan konfigurasi dari backup.',
                ],
                'completion_criteria' => [
                    'Topologi sesuai dengan kebutuhan organisasi yang dijelaskan.',
                    'Alamat jaringan tidak saling tumpang tindih.',
                    'Aturan keamanan mempunyai alasan yang jelas.',
                    'Administrasi dan backup dapat dilakukan menggunakan langkah yang terdokumentasi.',
                    'Pengujian hanya dilakukan pada lab, simulator, atau jaringan yang diizinkan.',
                ],
            ],
            [
                'career' => 'Psikologi',
                'area' => 'Psikologi Industri dan Organisasi',
                'title' => 'Employee & Organizational Assessment',
                'slug' => 'employee-organizational-assessment',
                'summary' => 'Menganalisis kasus organisasi dengan memisahkan data, interpretasi, dan rekomendasi intervensi.',
                'problem_statement' => 'Gunakan kasus organisasi fiktif mengenai masalah seperti motivasi menurun, komunikasi tim buruk, atau turnover tinggi. Analisis perilaku karyawan dan faktor organisasi, tentukan data tambahan yang diperlukan, lalu pilih bentuk asesmen serta intervensi yang sesuai. Proyek ini bukan diagnosis klinis.',
                'estimated_hours' => 16,
                'minimum_features' => [
                    'Tuliskan konteks organisasi, masalah utama, dan informasi yang sudah diketahui.',
                    'Pisahkan faktor individu, faktor pekerjaan, dan faktor organisasi yang mungkin berpengaruh.',
                    'Tentukan pertanyaan atau data tambahan yang dibutuhkan untuk memahami masalah.',
                    'Pilih metode psychological assessment yang sesuai dan jelaskan batas penggunaannya.',
                    'Susun rencana organizational development berdasarkan hasil analisis.',
                    'Buat indikator yang dapat digunakan untuk melihat apakah intervensi menghasilkan perubahan.',
                ],
                'stretch_features' => [
                    'Buat contoh kuesioner nonklinis untuk mengumpulkan informasi organisasi.',
                    'Tambahkan rencana komunikasi perubahan kepada karyawan.',
                ],
                'completion_criteria' => [
                    'Kasus menggunakan data fiktif atau data yang memang diizinkan.',
                    'Interpretasi dibedakan dari fakta yang tersedia.',
                    'Metode asesmen dipilih sesuai tujuan dan keterbatasannya disebutkan.',
                    'Rekomendasi tidak membuat diagnosis di luar kompetensi.',
                    'Keberhasilan intervensi mempunyai indikator yang dapat diamati.',
                ],
            ],
            [
                'career' => 'Psikologi',
                'area' => 'Konseling',
                'title' => 'Counseling Case Simulation',
                'slug' => 'counseling-case-simulation',
                'summary' => 'Melatih proses percakapan konseling melalui active listening, komunikasi interpersonal, dan pengelolaan emosi.',
                'problem_statement' => 'Gunakan kasus fiktif non-darurat mengenai masalah akademik, pekerjaan, atau hubungan interpersonal. Buat simulasi sesi konseling yang menunjukkan bagaimana konselor mendengarkan, mengklarifikasi masalah, memantulkan perasaan, dan membantu klien menyusun langkah berikutnya tanpa menghakimi atau memaksakan keputusan.',
                'estimated_hours' => 12,
                'minimum_features' => [
                    'Tuliskan latar belakang kasus fiktif dan tujuan sesi.',
                    'Buat pembukaan yang menjelaskan batas serta tujuan percakapan.',
                    'Gunakan pertanyaan terbuka, paraphrasing, refleksi perasaan, dan rangkuman.',
                    'Tunjukkan komunikasi interpersonal yang tidak menghakimi.',
                    'Identifikasi emosi yang muncul dan jelaskan bagaimana respons konselor menanganinya.',
                    'Buat refleksi setelah simulasi mengenai bagian yang sudah baik dan bagian yang perlu diperbaiki.',
                ],
                'stretch_features' => [
                    'Buat dua versi respons untuk menunjukkan perbedaan komunikasi efektif dan tidak efektif.',
                    'Tambahkan checklist keterampilan konseling untuk mengevaluasi simulasi.',
                ],
                'completion_criteria' => [
                    'Kasus dan identitas yang digunakan bersifat fiktif atau telah dianonimkan.',
                    'Percakapan menunjukkan keterampilan konseling secara nyata.',
                    'Respons tidak memberikan diagnosis atau klaim klinis tanpa dasar.',
                    'Bahasa yang digunakan tidak memaksa atau merendahkan pihak dalam kasus.',
                    'Refleksi menjelaskan alasan pemilihan respons.',
                ],
            ],
            [
                'career' => 'Psikologi',
                'area' => 'Penelitian Psikologi',
                'title' => 'Mini Psychological Research',
                'slug' => 'mini-psychological-research',
                'summary' => 'Menjalankan penelitian psikologi sederhana dari pertanyaan penelitian sampai analisis dan kesimpulan.',
                'problem_statement' => 'Pilih topik psikologi nonklinis yang aman untuk penelitian mahasiswa. Buat pertanyaan penelitian, tentukan metode, susun instrumen, kumpulkan data sukarela atau gunakan data simulasi yang dinyatakan dengan jelas, lalu lakukan analisis. Jangan membuat kesimpulan lebih luas daripada data yang tersedia.',
                'estimated_hours' => 20,
                'minimum_features' => [
                    'Susun latar belakang singkat, rumusan masalah, dan tujuan penelitian.',
                    'Tentukan variabel atau fokus penelitian, populasi, sampling, serta metode pengumpulan data.',
                    'Buat instrumen survei, wawancara, atau lembar observasi sesuai kebutuhan.',
                    'Jelaskan informed consent, anonimisasi, dan cara menjaga data partisipan.',
                    'Lakukan analisis deskriptif dan tampilkan hasil dalam tabel atau grafik.',
                    'Tuliskan pembahasan, keterbatasan, dan kesimpulan berdasarkan hasil yang benar-benar ditemukan.',
                ],
                'stretch_features' => [
                    'Tambahkan analisis hubungan sederhana jika data mendukung.',
                    'Bandingkan hasil dengan satu atau dua referensi penelitian sebelumnya.',
                ],
                'completion_criteria' => [
                    'Metode sesuai dengan pertanyaan penelitian.',
                    'Data pribadi yang tidak dibutuhkan tidak dikumpulkan.',
                    'Analisis dapat ditelusuri ke data.',
                    'Pembahasan membedakan hasil dan interpretasi.',
                    'Kesimpulan tidak melakukan diagnosis atau generalisasi yang tidak didukung.',
                ],
            ],
            [
                'career' => 'Ilmu Komunikasi',
                'area' => 'Public Relations',
                'title' => 'Crisis Communication Simulation',
                'slug' => 'crisis-communication-simulation',
                'summary' => 'Menyusun respons komunikasi krisis yang konsisten untuk media, stakeholder, dan kanal perusahaan.',
                'problem_statement' => 'Gunakan kasus perusahaan fiktif yang sedang menghadapi masalah reputasi, gangguan layanan, atau kesalahan operasional. Tentukan informasi yang sudah terverifikasi, stakeholder yang perlu diberi informasi, lalu buat respons awal, media handling, dan rencana komunikasi lanjutan.',
                'estimated_hours' => 14,
                'minimum_features' => [
                    'Rangkum krisis, fakta yang sudah diketahui, dan informasi yang masih perlu diverifikasi.',
                    'Petakan stakeholder berdasarkan kebutuhan informasi dan tingkat dampaknya.',
                    'Buat holding statement untuk beberapa jam pertama.',
                    'Buat media brief dan minimal delapan pertanyaan sulit beserta jawaban yang aman dan faktual.',
                    'Susun pesan corporate communication untuk internal dan eksternal.',
                    'Buat timeline komunikasi krisis sampai tahap pemulihan.',
                ],
                'stretch_features' => [
                    'Buat simulasi konferensi pers atau media interview.',
                    'Tambahkan rancangan monitoring sentimen setelah respons dipublikasikan.',
                ],
                'completion_criteria' => [
                    'Holding statement tidak menyatakan fakta yang belum dikonfirmasi.',
                    'Pesan antar-kanal tidak saling bertentangan.',
                    'Respons media menjawab pertanyaan tanpa berspekulasi.',
                    'Stakeholder utama mempunyai kanal komunikasi yang jelas.',
                    'Timeline mencakup pembaruan setelah situasi berubah.',
                ],
            ],
            [
                'career' => 'Ilmu Komunikasi',
                'area' => 'Jurnalistik',
                'title' => 'News Reporting Project',
                'slug' => 'news-reporting-project',
                'summary' => 'Membuat karya jurnalistik dari riset, wawancara, verifikasi fakta, penulisan, sampai laporan akhir.',
                'problem_statement' => 'Pilih topik yang dapat diliput secara aman dan legal. Tentukan pertanyaan utama, cari sumber yang relevan, lakukan wawancara dengan persetujuan narasumber, verifikasi informasi, kemudian tulis berita. Bila menggunakan skenario simulasi, nyatakan dengan jelas bahwa materi tersebut adalah simulasi.',
                'estimated_hours' => 16,
                'minimum_features' => [
                    'Susun angle berita, 5W+1H, serta daftar informasi yang harus diverifikasi.',
                    'Gunakan minimal tiga sumber informasi yang berbeda bila memungkinkan.',
                    'Buat pedoman wawancara dan lakukan wawancara atau simulasi yang terdokumentasi.',
                    'Pilih kutipan berdasarkan relevansi dan jangan mengubah maknanya.',
                    'Tulis artikel sekitar 500 sampai 800 kata menggunakan struktur jurnalistik yang sesuai.',
                    'Buat catatan verifikasi yang menunjukkan sumber setiap fakta utama.',
                ],
                'stretch_features' => [
                    'Buat versi multimedia dengan foto, grafik, atau video singkat.',
                    'Buat headline alternatif dan jelaskan perbedaannya.',
                ],
                'completion_criteria' => [
                    'Lead menjelaskan informasi terpenting secara jelas.',
                    'Fakta utama mempunyai sumber yang dapat ditelusuri.',
                    'Opini tidak disajikan sebagai fakta.',
                    'Kutipan mempunyai konteks yang cukup.',
                    'Tulisan telah diperiksa dari sisi konsistensi nama, angka, tanggal, dan sumber.',
                ],
            ],
            [
                'career' => 'Ilmu Komunikasi',
                'area' => 'Digital Media',
                'title' => 'Digital Content Campaign',
                'slug' => 'digital-content-campaign',
                'summary' => 'Membuat kampanye digital yang menggabungkan strategi konten, pengelolaan media sosial, dan produksi video.',
                'problem_statement' => 'Pilih brand, organisasi, atau kampanye fiktif dengan tujuan komunikasi yang jelas. Tentukan audiens, pesan utama, kalender konten, dan bentuk visual. Buat beberapa konten nyata termasuk video pendek, lalu tentukan cara mengukur apakah konten tersebut mencapai tujuan.',
                'estimated_hours' => 16,
                'minimum_features' => [
                    'Definisikan tujuan kampanye, target audiens, dan pesan utama.',
                    'Buat content pillar serta kalender publikasi minimal tujuh hari.',
                    'Buat minimal tiga aset konten digital dengan format berbeda.',
                    'Susun caption, call to action, dan aturan respons terhadap komentar pengguna.',
                    'Buat storyboard dan video berdurasi sekitar 60 sampai 90 detik.',
                    'Tentukan metrik performa dan buat contoh evaluasi hasil kampanye.',
                ],
                'stretch_features' => [
                    'Buat variasi konten untuk dua platform berbeda.',
                    'Tambahkan template laporan performa mingguan.',
                ],
                'completion_criteria' => [
                    'Setiap konten mempunyai tujuan yang jelas.',
                    'Tone dan tampilan konsisten dengan audiens serta pesan kampanye.',
                    'Video mempunyai struktur pembuka, isi, dan penutup.',
                    'Kalender konten dapat dijalankan tanpa informasi penting yang hilang.',
                    'Metrik yang dipilih berhubungan langsung dengan tujuan kampanye.',
                ],
            ],
        ];
    }
}
