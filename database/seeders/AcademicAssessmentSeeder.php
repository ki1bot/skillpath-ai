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
                'si-sql-data-processing' => ['Sebuah tabel transaksi memiliki customer_id dan total. Query yang paling tepat untuk menghitung total belanja setiap customer adalah?', ['SELECT customer_id, SUM(total) FROM transaksi GROUP BY customer_id', 'SELECT * FROM transaksi ORDER BY customer_id', 'DELETE FROM transaksi WHERE customer_id IS NOT NULL', 'ALTER TABLE transaksi ADD total_belanja INT'], 'A'],
                'si-spreadsheet-data-analysis' => ['Anda memiliki ribuan baris penjualan dan ingin merangkum total per kategori tanpa menulis rumus satu per satu. Fitur spreadsheet yang paling tepat adalah?', ['Conditional Formatting', 'Pivot Table', 'Freeze Panes', 'Data Validation'], 'B'],
                'si-business-intelligence-data-visualization' => ['Dashboard manajemen ingin memantau pencapaian penjualan terhadap target bulanan. Komponen BI yang paling relevan adalah?', ['KPI yang menampilkan aktual, target, dan selisih', 'Daftar seluruh transaksi mentah tanpa ringkasan', 'Dokumen naratif tanpa metrik', 'Tabel tanpa periode waktu'], 'A'],
                'si-data-visualization' => ['Anda ingin membandingkan nilai penjualan dari beberapa kategori pada periode yang sama. Visualisasi yang paling tepat adalah?', ['Bar chart', 'Pie chart dengan puluhan kategori', 'Teks paragraf', 'Scatter plot tanpa dua variabel numerik'], 'A'],
                'si-scenario-based-data-analysis' => ['Penjualan turun 20% pada satu wilayah. Langkah analisis awal yang paling tepat adalah?', ['Membandingkan tren, produk, pelanggan, dan periode untuk mencari penyebab yang terukur', 'Langsung menyimpulkan tim sales gagal', 'Menghapus data wilayah tersebut', 'Mengganti dashboard tanpa memeriksa data'], 'A'],
                'si-database-management' => ['Dalam database relasional, foreign key terutama digunakan untuk?', ['Menghubungkan data antar tabel dan menjaga integritas referensial', 'Mengubah seluruh kolom menjadi teks', 'Menggantikan semua primary key', 'Membuat antarmuka pengguna'], 'A'],
                'si-web-development' => ['Frontend perlu mengambil daftar produk dari backend tanpa memuat ulang seluruh halaman. Pendekatan yang paling tepat adalah?', ['Memanggil endpoint API melalui HTTP', 'Menulis data produk di CSS', 'Menjalankan query database langsung dari browser pengguna', 'Menyimpan seluruh data di gambar'], 'A'],
                'si-system-analysis-design' => ['Sebelum membangun sistem baru, proses bisnis tiap divisi ternyata berbeda. Langkah awal analis sistem yang paling tepat adalah?', ['Menggali kebutuhan dan memetakan proses yang sedang berjalan', 'Langsung memilih framework', 'Langsung membuat database production', 'Menghapus proses lama tanpa analisis'], 'A'],
                'si-erd-uml' => ['Diagram yang paling tepat untuk memodelkan entitas, atribut, dan hubungan antar data adalah?', ['ERD', 'Use case diagram', 'Gantt chart', 'Flowchart jaringan'], 'A'],
                'si-problem-solving' => ['Sebuah fitur sering gagal hanya pada kondisi tertentu. Pendekatan problem solving yang paling tepat adalah?', ['Mereproduksi masalah, mengisolasi penyebab, menguji hipotesis, lalu memverifikasi perbaikan', 'Mengubah banyak komponen sekaligus tanpa pengujian', 'Mengabaikan log karena fitur kadang berhasil', 'Langsung menyalahkan pengguna'], 'A'],
                'si-ui-design' => ['Tombol utama dan tombol sekunder memiliki tampilan sama kuat sehingga pengguna bingung. Prinsip UI yang perlu diperbaiki adalah?', ['Hierarki visual', 'Normalisasi database', 'Routing jaringan', 'Version control'], 'A'],
                'si-wireframing-prototyping' => ['Tujuan utama wireframe pada tahap awal desain adalah?', ['Memvalidasi struktur halaman dan prioritas konten sebelum detail visual', 'Menentukan password database', 'Mengukur bandwidth', 'Menentukan versi compiler'], 'A'],
                'si-prototyping' => ['Prototype interaktif paling berguna untuk?', ['Menguji alur dan interaksi sebelum produk dibangun penuh', 'Menggantikan seluruh riset pengguna', 'Menyimpan data transaksi production', 'Mengkonfigurasi server'], 'A'],
                'si-user-research' => ['Tim ingin memahami kebutuhan dan hambatan pengguna sebelum merancang fitur. Metode yang paling tepat adalah?', ['Wawancara atau observasi pengguna yang relevan', 'Menebak kebutuhan berdasarkan preferensi tim', 'Menyalin fitur kompetitor tanpa riset', 'Mengubah warna antarmuka secara acak'], 'A'],
                'si-usability' => ['Pengguna sering gagal menyelesaikan checkout. Metode yang paling langsung untuk menemukan masalah penggunaan adalah?', ['Usability testing dengan observasi pengguna', 'Mengganti logo', 'Menambah fitur baru tanpa data', 'Menghapus halaman bantuan'], 'A'],
            ],

            'Manajemen' => [
                'man-branding' => ['Sebuah merek ingin dikenal sebagai produk premium yang sederhana dan tahan lama. Hal ini terutama berkaitan dengan?', ['Brand positioning', 'Payroll', 'Inventory turnover', 'Recruitment funnel'], 'A'],
                'man-digital-marketing' => ['Kampanye iklan digital bertujuan menghasilkan pendaftaran. Metrik yang paling langsung untuk mengevaluasi tujuan tersebut adalah?', ['Conversion rate pendaftaran', 'Jumlah warna pada banner', 'Ukuran logo', 'Jumlah halaman dokumen internal'], 'A'],
                'man-market-research' => ['Perusahaan ingin mengetahui alasan pelanggan berpindah ke kompetitor. Data yang paling relevan dikumpulkan melalui?', ['Wawancara atau survei pelanggan yang berpindah', 'Daftar warna kantor', 'Jumlah perangkat karyawan', 'Nama file laporan lama'], 'A'],
                'man-marketing-strategy' => ['Setelah menentukan target pasar, langkah strategis yang paling tepat adalah?', ['Menetapkan positioning, value proposition, kanal, dan tujuan pemasaran yang terukur', 'Menjalankan semua kanal tanpa prioritas', 'Membuat konten tanpa memahami audiens', 'Mengabaikan pesaing dan data pasar'], 'A'],
                'man-campaign-analysis' => ['Sebuah kampanye memiliki banyak impresi tetapi hampir tidak ada konversi. Analisis yang paling relevan adalah?', ['Mengevaluasi funnel dari impresi, klik, landing page, hingga konversi', 'Mengganti nama perusahaan saja', 'Menambah anggaran tanpa melihat data', 'Mengabaikan conversion rate'], 'A'],
                'man-financial-planning' => ['Dalam financial planning, proyeksi arus kas terutama digunakan untuk?', ['Memperkirakan kemampuan memenuhi kebutuhan kas pada periode mendatang', 'Menentukan warna merek', 'Menyusun struktur organisasi', 'Menilai kualitas wawancara'], 'A'],
                'man-financial-analysis' => ['Analisis tren laporan keuangan dilakukan terutama untuk?', ['Melihat perubahan kinerja dan posisi keuangan dari waktu ke waktu', 'Menentukan slogan pemasaran', 'Menilai desain antarmuka', 'Mengatur topologi jaringan'], 'A'],
                'man-financial-ratios' => ['Current ratio terutama digunakan untuk menilai?', ['Likuiditas jangka pendek perusahaan', 'Efektivitas desain logo', 'Kualitas rekrutmen', 'Engagement media sosial'], 'A'],
                'man-investment-management' => ['Prinsip dasar hubungan risiko dan imbal hasil dalam investasi adalah?', ['Potensi imbal hasil yang lebih tinggi umumnya disertai risiko yang lebih tinggi', 'Semua investasi memberikan hasil yang pasti', 'Risiko tidak perlu dipertimbangkan', 'Diversifikasi selalu menghapus seluruh risiko'], 'A'],
                'man-financial-decision-making' => ['Dua alternatif investasi memiliki manfaat yang sama, tetapi satu membutuhkan biaya lebih rendah dan risiko lebih terkendali. Keputusan yang lebih rasional adalah?', ['Memilih alternatif dengan biaya dan risiko lebih rendah setelah asumsi diverifikasi', 'Memilih secara acak', 'Mengabaikan biaya', 'Memilih yang paling mahal tanpa analisis'], 'A'],
                'man-recruitment-selection' => ['Sebelum membuka lowongan, langkah rekrutmen yang paling tepat adalah?', ['Menetapkan kebutuhan jabatan dan profil kandidat', 'Mengiklankan posisi tanpa deskripsi kerja', 'Memilih kandidat pertama yang datang', 'Mengabaikan kebutuhan organisasi'], 'A'],
                'man-candidate-selection' => ['Agar proses seleksi kandidat konsisten dan adil, perusahaan sebaiknya?', ['Menggunakan kriteria kompetensi dan metode penilaian yang terstruktur', 'Mengandalkan intuisi pewawancara saja', 'Memilih berdasarkan foto profil', 'Mengubah kriteria untuk setiap kandidat'], 'A'],
                'man-interview' => ['Pertanyaan wawancara berbasis perilaku biasanya meminta kandidat untuk?', ['Menjelaskan contoh situasi nyata, tindakan, dan hasil yang pernah dialami', 'Menebak jawaban yang disukai pewawancara', 'Membaca ulang CV tanpa penjelasan', 'Menjawab hanya ya atau tidak untuk semua kompetensi'], 'A'],
                'man-performance-management' => ['Sasaran kinerja yang baik seharusnya?', ['Spesifik, terukur, relevan, dan memiliki batas waktu', 'Berubah setiap hari tanpa alasan', 'Tidak memiliki indikator', 'Hanya diketahui atasan'], 'A'],
                'man-talent-management' => ['Succession planning bertujuan untuk?', ['Menyiapkan talenta bagi peran penting di masa depan', 'Menghapus semua program pengembangan', 'Mengganti seluruh karyawan setiap tahun', 'Menghindari evaluasi kompetensi'], 'A'],
            ],

            'Teknik Informatika' => [
                'ti-algorithms-data-structures' => ['Sebuah algoritma pencarian harus bekerja pada data yang sudah terurut. Algoritma yang secara umum lebih efisien daripada linear search adalah?', ['Binary search', 'Bubble sort', 'Depth-first search pada semua kasus', 'Random search'], 'A'],
                'ti-data-structures' => ['Struktur data dengan pola Last In First Out adalah?', ['Queue', 'Stack', 'Graph', 'Heap'], 'B'],
                'ti-object-oriented-programming' => ['Menyembunyikan detail internal objek dan menyediakan akses melalui antarmuka terkontrol disebut?', ['Encapsulation', 'Recursion', 'Compilation', 'Serialization'], 'A'],
                'ti-software-engineering' => ['Requirement berubah ketika pengembangan sudah berjalan. Praktik yang paling tepat adalah?', ['Menganalisis dampak, memperbarui requirement, lalu menyesuaikan rencana implementasi', 'Mengabaikan perubahan', 'Menghapus seluruh source code', 'Menerapkan langsung ke production tanpa tes'], 'A'],
                'ti-debugging' => ['Program menghasilkan output salah hanya untuk input tertentu. Langkah debugging yang paling efektif adalah?', ['Mereproduksi kasus gagal lalu menelusuri nilai dan alur eksekusi', 'Mengubah kode secara acak', 'Menghapus semua validasi', 'Langsung merilis ulang tanpa tes'], 'A'],
                'ti-computer-networks' => ['Perangkat akan mengirim paket ke jaringan di luar subnet lokal. Paket biasanya terlebih dahulu diarahkan ke?', ['Default gateway', 'Loopback address', 'Alamat broadcast aplikasi', 'Port USB'], 'A'],
                'ti-operating-systems' => ['Virtual memory memungkinkan sistem operasi untuk?', ['Menggunakan penyimpanan sebagai perluasan logis memori utama saat diperlukan', 'Menghilangkan kebutuhan CPU', 'Menjalankan jaringan tanpa protokol', 'Menghapus semua proses latar belakang'], 'A'],
                'ti-network-troubleshooting' => ['Sebuah komputer bisa melakukan ping ke gateway tetapi tidak bisa membuka nama domain. Komponen yang paling relevan diperiksa adalah?', ['DNS', 'Keyboard', 'GPU', 'Printer'], 'A'],
                'ti-cybersecurity' => ['Prinsip least privilege berarti?', ['Memberikan hak akses minimum yang diperlukan untuk tugas', 'Memberikan akses administrator ke semua pengguna', 'Menyimpan password sebagai teks biasa', 'Menonaktifkan logging'], 'A'],
                'ti-system-administration' => ['Administrator ingin memastikan layanan server otomatis kembali berjalan setelah reboot. Hal yang paling tepat dilakukan adalah?', ['Mengaktifkan service pada service manager dan memverifikasi statusnya', 'Membuka editor teks setiap boot', 'Menghapus log layanan', 'Mengganti alamat MAC'], 'A'],
                'ti-machine-learning' => ['Model sangat baik pada data training tetapi buruk pada data baru. Kondisi ini disebut?', ['Underfitting', 'Overfitting', 'Normalization', 'Clustering'], 'B'],
                'ti-data-science' => ['Dataset memiliki banyak nilai kosong sebelum analisis. Langkah yang paling tepat adalah?', ['Menganalisis pola missing value lalu menentukan penanganan yang sesuai', 'Mengabaikan kualitas data', 'Mengganti semua nilai dengan angka acak', 'Menghapus target analisis'], 'A'],
                'ti-statistics' => ['Median lebih tepat daripada mean untuk merangkum data pendapatan ketika?', ['Data memiliki outlier ekstrem dan distribusi sangat menceng', 'Semua nilai sama', 'Data tidak memiliki angka', 'Jumlah observasi nol'], 'A'],
                'ti-model-evaluation' => ['Pada klasifikasi dengan kelas positif yang sangat jarang, metrik yang lebih informatif daripada accuracy saja adalah?', ['Precision, recall, dan F1-score', 'Ukuran file model', 'Jumlah kolom CSV', 'Waktu membuka editor'], 'A'],
                'ti-computer-vision' => ['Dalam klasifikasi citra, data augmentation umumnya digunakan untuk?', ['Menambah variasi data training secara terkontrol', 'Menghapus seluruh label', 'Menggantikan evaluasi model', 'Mengubah tugas menjadi routing jaringan'], 'A'],
            ],

            'Sistem Komputer' => [
                'sk-computer-architecture' => ['Komponen yang mengeksekusi instruksi dan operasi aritmetika-logika terutama berada pada?', ['CPU', 'Power supply', 'Monitor', 'Keyboard'], 'A'],
                'sk-digital-logic' => ['Gerbang AND menghasilkan keluaran 1 ketika?', ['Semua input bernilai 1', 'Minimal satu input bernilai 1', 'Semua input bernilai 0', 'Input selalu berbeda'], 'A'],
                'sk-processor' => ['Register pada processor digunakan terutama untuk?', ['Menyimpan data atau instruksi sementara yang sedang diproses', 'Menyimpan arsip bertahun-tahun', 'Menggantikan seluruh RAM', 'Menghubungkan monitor ke listrik'], 'A'],
                'sk-memory' => ['Perbedaan umum RAM dan penyimpanan sekunder adalah?', ['RAM bersifat volatil dan digunakan untuk data kerja aktif', 'RAM selalu lebih lambat daripada hard disk', 'Penyimpanan sekunder hilang saat listrik mati', 'Keduanya selalu memiliki fungsi identik'], 'A'],
                'sk-microprocessor-microcontroller' => ['Mikroprosesor pada sistem komputer terutama berfungsi sebagai?', ['Unit pemrosesan yang mengeksekusi instruksi', 'Sensor suhu', 'Perangkat keluaran mekanik', 'Media transmisi jaringan'], 'A'],
                'sk-microcontroller' => ['Mikrokontroler umumnya mengintegrasikan komponen apa dalam satu chip?', ['CPU, memori, dan peripheral I/O', 'Monitor, keyboard, dan printer', 'Router, switch, dan access point', 'Database, browser, dan web server'], 'A'],
                'sk-embedded-systems' => ['Karakteristik utama embedded system adalah?', ['Dirancang untuk fungsi tertentu dengan resource yang terkontrol', 'Harus menjalankan semua aplikasi desktop', 'Selalu membutuhkan monitor besar', 'Tidak dapat berinteraksi dengan hardware'], 'A'],
                'sk-internet-of-things' => ['Agar perangkat IoT dapat bertukar data dengan layanan lain, komponen yang paling penting adalah?', ['Konektivitas dan protokol komunikasi yang sesuai', 'Ukuran font dashboard', 'Nama file source code', 'Warna casing perangkat'], 'A'],
                'sk-sensor-actuator-integration' => ['Fungsi utama sensor dalam sistem embedded adalah?', ['Mendeteksi atau mengukur kondisi fisik dan mengubahnya menjadi data', 'Menghasilkan gerakan mekanik sebagai output', 'Menyimpan seluruh source code', 'Menggantikan koneksi jaringan'], 'A'],
                'sk-actuator' => ['Fungsi aktuator dalam sistem kendali adalah?', ['Mengubah sinyal kontrol menjadi aksi fisik', 'Mengukur suhu tanpa menghasilkan aksi', 'Mengompilasi program', 'Menyimpan konfigurasi database'], 'A'],
                'sk-computer-networks' => ['Perangkat yang umumnya meneruskan paket antar jaringan berbeda adalah?', ['Router', 'Keyboard', 'Speaker', 'Scanner'], 'A'],
                'sk-network-administration' => ['Layanan yang umum digunakan untuk memberikan alamat IP otomatis kepada client adalah?', ['DHCP', 'HTML', 'CSS', 'JPEG'], 'A'],
                'sk-network-security' => ['Tujuan segmentasi jaringan dalam keamanan adalah?', ['Membatasi penyebaran akses dan trafik antar bagian jaringan', 'Membuat semua perangkat memakai satu hak akses', 'Menonaktifkan seluruh monitoring', 'Menghapus kebutuhan autentikasi'], 'A'],
                'sk-firewall' => ['Firewall digunakan terutama untuk?', ['Mengizinkan atau memblokir trafik berdasarkan aturan keamanan', 'Meningkatkan kapasitas RAM', 'Menyimpan file multimedia', 'Mengedit source code'], 'A'],
                'sk-threat-detection' => ['Indikasi login gagal berulang dari banyak alamat IP dalam waktu singkat sebaiknya diperlakukan sebagai?', ['Sinyal yang perlu dianalisis sebagai potensi serangan', 'Bukti bahwa sistem pasti aman', 'Alasan menghapus seluruh log', 'Aktivitas yang selalu normal'], 'A'],
            ],

            'Psikologi' => [
                'psi-employee-behavior' => ['Karyawan kehilangan motivasi setelah merasa usahanya tidak pernah diakui. Faktor organisasi yang paling relevan dievaluasi adalah?', ['Sistem penghargaan dan umpan balik', 'Resolusi layar', 'Topologi jaringan', 'Struktur tabel database'], 'A'],
                'psi-organizational-behavior' => ['Konflik antar tim terus berulang karena tujuan dan peran tidak jelas. Analisis organizational behavior sebaiknya berfokus pada?', ['Struktur peran, komunikasi, norma, dan dinamika antar kelompok', 'Warna seragam', 'Jenis database', 'Kecepatan internet rumah'], 'A'],
                'psi-work-style-assessment' => ['Work-style assessment paling tepat digunakan untuk?', ['Memahami kecenderungan cara seseorang bekerja dan berkolaborasi', 'Mendiagnosis gangguan klinis tanpa prosedur profesional', 'Menentukan gaji hanya dari satu skor', 'Menggantikan seluruh proses wawancara'], 'A'],
                'psi-psychological-assessment' => ['Instrumen psychological assessment sebaiknya dipilih berdasarkan?', ['Tujuan, validitas, reliabilitas, etika, dan kesesuaian penggunaan', 'Popularitas di media sosial', 'Warna instrumen', 'Kemudahan mendapat skor tinggi'], 'A'],
                'psi-organizational-development' => ['Sebelum menjalankan intervensi organizational development, langkah yang paling tepat adalah?', ['Melakukan diagnosis kebutuhan organisasi berdasarkan data', 'Langsung mengganti seluruh struktur', 'Menyalin program organisasi lain tanpa analisis', 'Menghindari stakeholder'], 'A'],
                'psi-interpersonal-communication' => ['Ketika terjadi salah paham dalam percakapan, langkah komunikasi yang paling konstruktif adalah?', ['Mengklarifikasi makna pesan dan mendengarkan perspektif pihak lain', 'Menaikkan nada bicara', 'Menghindari pembicaraan selamanya', 'Menganggap niat pihak lain tanpa bertanya'], 'A'],
                'psi-counseling-skills' => ['Klien berkata, “Saya merasa gagal dan tidak tahu harus mulai dari mana.” Respons yang paling menunjukkan active listening adalah?', ['“Kamu merasa kewalahan dan sulit melihat langkah berikutnya, benar begitu?”', '“Jangan dipikirkan.”', '“Kamu harus langsung mengambil keputusan.”', '“Masalah itu sederhana.”'], 'A'],
                'psi-empathy' => ['Respons yang paling menunjukkan empathy adalah?', ['Mencoba memahami pengalaman dari sudut pandang orang lain tanpa terburu-buru menghakimi', 'Langsung membandingkan masalah dengan diri sendiri', 'Mengabaikan emosi lawan bicara', 'Memberi nasihat sebelum mendengar cerita'], 'A'],
                'psi-emotional-intelligence' => ['Seseorang menyadari dirinya sedang marah sebelum merespons konflik. Kemampuan ini terutama menunjukkan?', ['Self-awareness sebagai bagian dari emotional intelligence', 'Data visualization', 'Recruitment', 'Network routing'], 'A'],
                'psi-counseling-scenario' => ['Dalam simulasi konseling, klien mulai terdiam setelah menceritakan hal yang berat. Respons awal yang paling tepat adalah?', ['Memberi ruang, menunjukkan perhatian, lalu mengundang klien melanjutkan ketika siap', 'Memaksa klien menjawab cepat', 'Mengganti topik tanpa alasan', 'Mengakhiri sesi seketika'], 'A'],
                'psi-research-methodology' => ['Peneliti ingin melihat hubungan kualitas tidur dan stres tanpa manipulasi variabel. Desain awal yang paling sesuai adalah?', ['Studi korelasional', 'Eksperimen wajib', 'Studi tanpa variabel terukur', 'Observasi tanpa pencatatan'], 'A'],
                'psi-interview-observation' => ['Dalam interview penelitian, pertanyaan terbuka berguna terutama untuk?', ['Mendorong responden menjelaskan pengalaman dengan lebih kaya', 'Memastikan semua jawaban hanya ya/tidak', 'Menghilangkan kebutuhan pencatatan', 'Mengarahkan responden ke jawaban tertentu'], 'A'],
                'psi-observation' => ['Agar hasil observation lebih konsisten antar observer, peneliti sebaiknya?', ['Menggunakan definisi operasional dan pedoman coding yang jelas', 'Membiarkan setiap observer membuat definisi sendiri', 'Tidak mencatat waktu observasi', 'Mengubah kategori tanpa alasan'], 'A'],
                'psi-survey-data-analysis' => ['Pertanyaan survey yang baik sebaiknya?', ['Jelas, tidak ambigu, dan tidak menggiring responden', 'Menggabungkan banyak pertanyaan berbeda dalam satu kalimat', 'Selalu memaksa jawaban positif', 'Menggunakan istilah yang tidak dipahami responden'], 'A'],
                'psi-data-analysis' => ['Sebelum menarik kesimpulan dari data penelitian, langkah yang penting adalah?', ['Memeriksa kualitas data dan memilih analisis yang sesuai dengan pertanyaan penelitian', 'Menghapus hasil yang tidak sesuai harapan', 'Mengubah hipotesis setelah melihat hasil tanpa pelaporan', 'Menyimpulkan hanya dari satu responden'], 'A'],
            ],

            'Ilmu Komunikasi' => [
                'ikom-media-relations' => ['Saat mengirim press release kepada media, informasi yang paling penting adalah?', ['Fakta bernilai berita yang jelas, terverifikasi, dan memiliki narahubung', 'Pesan promosi tanpa data', 'Dokumen tanpa judul', 'Informasi yang belum dikonfirmasi'], 'A'],
                'ikom-corporate-communication' => ['Perusahaan mengumumkan perubahan kebijakan besar. Agar komunikasi konsisten, langkah yang paling tepat adalah?', ['Menyelaraskan pesan utama untuk stakeholder yang berbeda', 'Membiarkan tiap kanal memberi fakta berbeda', 'Menunda penjelasan tanpa batas', 'Menghapus semua saluran komunikasi'], 'A'],
                'ikom-crisis-communication' => ['Pada awal krisis, organisasi belum memiliki seluruh fakta. Respons komunikasi yang paling tepat adalah?', ['Menyampaikan fakta terverifikasi, menjelaskan yang masih diselidiki, dan memberi pembaruan', 'Mengarang detail agar terlihat cepat', 'Tidak merespons sama sekali', 'Menyalahkan pihak lain tanpa bukti'], 'A'],
                'ikom-public-communication' => ['Saat menyampaikan informasi kompleks kepada publik umum, pendekatan paling efektif adalah?', ['Menggunakan pesan utama yang jelas, bahasa sesuai audiens, dan struktur yang mudah diikuti', 'Menggunakan jargon sebanyak mungkin', 'Menyembunyikan tujuan komunikasi', 'Membaca data tanpa konteks'], 'A'],
                'ikom-reputation-management' => ['Reputasi organisasi memburuk akibat keluhan berulang. Langkah yang paling tepat adalah?', ['Mengidentifikasi akar masalah, memperbaiki layanan, dan mengomunikasikan tindakan secara konsisten', 'Menghapus semua komentar negatif tanpa evaluasi', 'Membuat klaim positif tanpa bukti', 'Mengabaikan keluhan'], 'A'],
                'ikom-news-writing' => ['Dalam penulisan hard news, informasi terpenting umumnya ditempatkan?', ['Di bagian awal dengan struktur piramida terbalik', 'Hanya di paragraf terakhir', 'Di caption tanpa isi berita', 'Secara acak'], 'A'],
                'ikom-journalistic-interview' => ['Untuk memperoleh jawaban mendalam dari narasumber, pewawancara sebaiknya lebih banyak menggunakan?', ['Pertanyaan terbuka yang spesifik dan relevan', 'Pertanyaan yang sudah mengandung jawaban', 'Pertanyaan di luar topik', 'Pertanyaan ya/tidak untuk semua hal'], 'A'],
                'ikom-news-reporting' => ['Sebelum mempublikasikan klaim penting dari satu sumber, reporter sebaiknya?', ['Melakukan verifikasi dan mencari konfirmasi atau bukti pendukung', 'Langsung menerbitkan karena sumber terdengar yakin', 'Menghapus konteks', 'Mengubah kutipan agar menarik'], 'A'],
                'ikom-fact-checking' => ['Sebuah unggahan viral memuat angka tanpa sumber. Langkah fact checking yang paling tepat adalah?', ['Menelusuri sumber asli, memeriksa konteks, dan membandingkan dengan sumber kredibel', 'Membagikannya karena sudah viral', 'Menilai hanya dari desain unggahan', 'Menganggap benar karena sesuai opini'], 'A'],
                'ikom-journalistic-ethics' => ['Dalam meliput korban yang rentan, praktik etis yang paling tepat adalah?', ['Meminimalkan dampak buruk, menghormati privasi, dan tetap menjaga akurasi', 'Mengekspos identitas sensitif demi klik', 'Mengubah kutipan tanpa izin', 'Membayar narasumber untuk mengarang fakta'], 'A'],
                'ikom-content-creation' => ['Sebelum membuat konten digital, langkah yang paling menentukan arah pesan adalah?', ['Menentukan tujuan komunikasi dan audiens', 'Memilih font tanpa tujuan', 'Mengunggah konten acak', 'Menyalin konten kompetitor sepenuhnya'], 'A'],
                'ikom-social-media-management' => ['Akun organisasi ingin meningkatkan interaksi bermakna. Metrik yang lebih berguna daripada jumlah follower saja adalah?', ['Engagement rate dan kualitas respons audiens', 'Ukuran file logo', 'Jumlah folder internal', 'Kecepatan CPU admin'], 'A'],
                'ikom-video-production' => ['Dokumen yang membantu merencanakan urutan visual sebelum produksi video adalah?', ['Storyboard atau shot list', 'Balance sheet', 'ERD', 'Routing table'], 'A'],
                'ikom-content-strategy' => ['Content strategy yang baik seharusnya menghubungkan?', ['Tujuan, audiens, pesan, kanal, format, dan metrik keberhasilan', 'Warna favorit tim saja', 'Jumlah posting tanpa tujuan', 'Semua tren tanpa seleksi'], 'A'],
                'ikom-audience-analysis' => ['Tujuan utama audience analysis adalah?', ['Memahami karakteristik, kebutuhan, perilaku, dan konteks audiens agar pesan lebih relevan', 'Menentukan password akun', 'Mengganti semua konten menjadi format sama', 'Menghindari penggunaan data'], 'A'],
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
            ->get()
            ->keyBy('name');

        foreach ($sets as $studyProgram => $questions) {
            $career = $careers->get($studyProgram);

            if (! $career) {
                continue;
            }

            $assessment = Assessment::updateOrCreate(
                [
                    'career_id' => $career->id,
                    'study_program' => $studyProgram,
                ],
                [
                    'title' => 'Assesment Awal '.$studyProgram,
                    'description' => 'Jawab 15 pertanyaan yang mewakili 3 bidang utama pada jurusan '.$studyProgram.'. Hasilnya digunakan untuk memperbarui profil kemampuan awal Anda.',
                    'duration_minutes' => 30,
                    'is_active' => true,
                ],
            );

            $assessmentSkillIds = collect(array_keys($questions))
                ->map(
                    fn (string $skillSlug) => $skills
                        ->get($skillSlug)
                        ?->id,
                )
                ->filter()
                ->values()
                ->all();

            $assessment
                ->questions()
                ->whereNotIn('skill_id', $assessmentSkillIds)
                ->delete();

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
                $correctIndex = $this->correctAnswerIndex($answer);

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

    private function correctAnswerIndex(string $answer): int
    {
        return match ($answer) {
            'B' => 1,
            'C' => 2,
            'D' => 3,
            default => 0,
        };
    }

    private function answerForIndex(int $index): string
    {
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
