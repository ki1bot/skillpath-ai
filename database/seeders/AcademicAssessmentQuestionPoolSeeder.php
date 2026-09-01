<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResult;
use App\Models\Skill;
use App\Support\AcademicAssessmentCatalog;
use Illuminate\Database\Seeder;
use RuntimeException;

class AcademicAssessmentQuestionPoolSeeder extends Seeder
{
    public function run(): void
    {
        $sets = [
            'Sistem Informasi' => [
                'si-sql-data-processing' => [
                    $this->question(
                        'Sebuah tabel transaksi memiliki customer_id dan total. Query yang paling tepat untuk menghitung total belanja setiap customer adalah?',
                        'SELECT customer_id, SUM(total) FROM transaksi GROUP BY customer_id',
                        'SELECT * FROM transaksi ORDER BY customer_id',
                        'DELETE FROM transaksi WHERE customer_id IS NOT NULL',
                        'ALTER TABLE transaksi ADD total_belanja INT',
                    ),
                    $this->question(
                        'Sebuah laporan hanya boleh menampilkan pelanggan yang total transaksinya lebih dari Rp10 juta setelah data dikelompokkan. Klausa SQL yang tepat adalah?',
                        'HAVING',
                        'ORDER BY',
                        'ALTER TABLE',
                        'DROP TABLE',
                    ),
                    $this->question(
                        'Anda ingin menampilkan seluruh pelanggan termasuk pelanggan yang belum pernah melakukan transaksi. Jenis JOIN yang paling tepat adalah?',
                        'LEFT JOIN dari tabel pelanggan ke tabel transaksi',
                        'INNER JOIN yang hanya mengambil data yang cocok',
                        'CROSS JOIN seluruh data',
                        'Tidak menggunakan JOIN sama sekali',
                    ),
                ],
                'si-spreadsheet-data-analysis' => [
                    $this->question(
                        'Anda memiliki ribuan baris penjualan dan ingin merangkum total per kategori tanpa menulis rumus satu per satu. Fitur spreadsheet yang paling tepat adalah?',
                        'Pivot Table',
                        'Freeze Panes',
                        'Conditional Formatting',
                        'Protect Sheet',
                    ),
                    $this->question(
                        'Dua tabel spreadsheet memiliki kode produk yang sama. Anda ingin mengambil nama produk dari tabel referensi berdasarkan kode tersebut. Fitur yang paling tepat adalah?',
                        'XLOOKUP atau fungsi lookup sejenis',
                        'Freeze Panes',
                        'Conditional Formatting',
                        'Protect Sheet',
                    ),
                    $this->question(
                        'Anda ingin menjumlahkan nilai penjualan hanya ketika wilayah dan kategori memenuhi kriteria tertentu. Fungsi spreadsheet yang paling sesuai adalah?',
                        'SUMIFS',
                        'LEFT',
                        'LEN',
                        'CONCAT',
                    ),
                ],
                'si-business-intelligence-data-visualization' => [
                    $this->question(
                        'Dashboard manajemen ingin memantau pencapaian penjualan terhadap target bulanan. Komponen BI yang paling relevan adalah?',
                        'KPI yang menampilkan aktual, target, dan selisih',
                        'Seluruh transaksi mentah tanpa ringkasan',
                        'Dokumen naratif tanpa metrik',
                        'Tabel tanpa periode waktu',
                    ),
                    $this->question(
                        'Manajemen ingin melihat penjualan berdasarkan waktu, wilayah, dan kategori produk secara interaktif. Pendekatan paling tepat adalah?',
                        'Membuat dashboard dengan dimensi, metrik, dan filter yang relevan',
                        'Menampilkan seluruh data mentah tanpa agregasi',
                        'Menyimpan laporan hanya dalam file teks',
                        'Menghapus data historis',
                    ),
                    $this->question(
                        'Visualisasi yang paling tepat untuk menunjukkan perubahan nilai penjualan dari Januari sampai Desember adalah?',
                        'Line chart',
                        'Pie chart tanpa dimensi waktu',
                        'Tabel tanpa urutan bulan',
                        'Diagram yang tidak memiliki sumbu waktu',
                    ),
                ],
                'si-database-management' => [
                    $this->question(
                        'Dalam database relasional, foreign key terutama digunakan untuk?',
                        'Menghubungkan data antar tabel dan menjaga integritas referensial',
                        'Mengubah semua kolom menjadi teks',
                        'Menggantikan semua primary key',
                        'Membuat antarmuka pengguna',
                    ),
                    $this->question(
                        'Pemindahan saldo harus berhasil seluruhnya atau dibatalkan seluruhnya jika salah satu proses gagal. Konsep database yang paling relevan adalah?',
                        'Transaction dan atomicity',
                        'CSS inheritance',
                        'HTTP caching',
                        'Image compression',
                    ),
                    $this->question(
                        'Kolom email sering digunakan pada kondisi WHERE dan pencarian mulai lambat ketika data bertambah besar. Optimasi database yang paling relevan adalah?',
                        'Membuat index yang sesuai pada kolom tersebut',
                        'Menghapus primary key',
                        'Mengubah seluruh tabel menjadi satu kolom',
                        'Menonaktifkan constraint',
                    ),
                ],
                'si-web-development' => [
                    $this->question(
                        'Frontend perlu mengambil daftar produk dari backend tanpa memuat ulang seluruh halaman. Pendekatan yang paling tepat adalah?',
                        'Memanggil endpoint API melalui HTTP',
                        'Menulis data produk di CSS',
                        'Menjalankan query database langsung dari browser',
                        'Menyimpan seluruh data di gambar',
                    ),
                    $this->question(
                        'Sebuah endpoint berhasil membuat resource baru pada server. Status HTTP yang paling sesuai adalah?',
                        '201 Created',
                        '404 Not Found',
                        '401 Unauthorized',
                        '500 Internal Server Error',
                    ),
                    $this->question(
                        'Data formulir dari browser akan disimpan ke database. Praktik backend yang paling tepat sebelum penyimpanan adalah?',
                        'Melakukan validasi input pada server',
                        'Mempercayai seluruh input dari browser',
                        'Menghapus seluruh aturan validasi',
                        'Menyimpan input sebelum diperiksa',
                    ),
                ],
                'si-system-analysis-design' => [
                    $this->question(
                        'Sebelum membangun sistem baru, proses bisnis tiap divisi ternyata berbeda. Langkah awal analis sistem yang paling tepat adalah?',
                        'Menggali kebutuhan dan memetakan proses yang sedang berjalan',
                        'Langsung memilih framework',
                        'Langsung membuat database production',
                        'Menghapus proses lama tanpa analisis',
                    ),
                    $this->question(
                        'Analis ingin mengetahui aktor dan interaksi utama pengguna dengan sistem sebelum detail teknis dibuat. Diagram yang paling sesuai adalah?',
                        'Use case diagram',
                        'Pie chart',
                        'Network topology',
                        'Bar chart',
                    ),
                    $this->question(
                        'Pernyataan "sistem harus memungkinkan pelanggan mengubah alamat pengiriman" termasuk jenis requirement?',
                        'Functional requirement',
                        'Hardware specification',
                        'Network topology',
                        'Visual branding guideline',
                    ),
                ],
                'si-ui-design' => [
                    $this->question(
                        'Tombol utama dan tombol sekunder memiliki tampilan sama kuat sehingga pengguna bingung. Prinsip UI yang perlu diperbaiki adalah?',
                        'Hierarki visual',
                        'Normalisasi database',
                        'Routing jaringan',
                        'Version control',
                    ),
                    $this->question(
                        'Teks abu-abu muda pada latar putih sulit dibaca. Aspek UI yang paling perlu diperbaiki adalah?',
                        'Kontras visual dan keterbacaan',
                        'Konfigurasi DNS',
                        'Algoritma sorting',
                        'Normalisasi database',
                    ),
                    $this->question(
                        'Komponen yang memiliki fungsi sama terlihat berbeda pada setiap halaman. Prinsip desain yang perlu diperbaiki adalah?',
                        'Konsistensi antarmuka',
                        'Database replication',
                        'Packet routing',
                        'Memory allocation',
                    ),
                ],
                'si-wireframing-prototyping' => [
                    $this->question(
                        'Tujuan utama wireframe pada tahap awal desain adalah?',
                        'Memvalidasi struktur halaman dan prioritas konten sebelum detail visual',
                        'Menentukan password database',
                        'Mengukur bandwidth',
                        'Menentukan versi compiler',
                    ),
                    $this->question(
                        'Tim ingin mencoba beberapa struktur halaman dengan cepat tanpa fokus pada warna dan ilustrasi. Artefak yang paling tepat adalah?',
                        'Low-fidelity wireframe',
                        'Database production',
                        'Final design system lengkap',
                        'Server monitoring dashboard',
                    ),
                    $this->question(
                        'Sebelum fitur dikembangkan, tim ingin mengetahui apakah pengguna memahami alur pemesanan. Prototype sebaiknya digunakan untuk?',
                        'Menguji alur dan interaksi dengan pengguna',
                        'Menggantikan seluruh backend',
                        'Menyimpan transaksi production',
                        'Mengatur firewall server',
                    ),
                ],
                'si-user-research' => [
                    $this->question(
                        'Tim ingin memahami kebutuhan dan hambatan pengguna sebelum merancang fitur. Metode yang paling tepat adalah?',
                        'Wawancara atau observasi pengguna yang relevan',
                        'Menebak kebutuhan berdasarkan preferensi tim',
                        'Menyalin fitur kompetitor tanpa riset',
                        'Mengubah desain secara acak',
                    ),
                    $this->question(
                        'Tim ingin mengetahui alasan pengguna berhenti pada tahap tertentu dalam aplikasi. Metode yang paling membantu adalah?',
                        'Wawancara pengguna disertai observasi perilaku',
                        'Menebak berdasarkan pendapat developer',
                        'Menghapus analytics',
                        'Mengubah seluruh produk tanpa riset',
                    ),
                    $this->question(
                        'Setelah wawancara beberapa pengguna, tim memiliki banyak catatan temuan. Langkah yang tepat untuk menemukan pola adalah?',
                        'Mengelompokkan temuan berdasarkan tema dan masalah yang berulang',
                        'Memilih satu komentar secara acak',
                        'Mengabaikan data yang berbeda',
                        'Menghapus seluruh catatan wawancara',
                    ),
                ],
            ],

            'Manajemen' => [
                'man-branding' => [
                    $this->question(
                        'Sebuah merek ingin dikenal sebagai produk premium yang sederhana dan tahan lama. Hal ini terutama berkaitan dengan?',
                        'Brand positioning',
                        'Payroll',
                        'Inventory turnover',
                        'Recruitment funnel',
                    ),
                    $this->question(
                        'Perusahaan menggunakan pesan, warna, dan gaya komunikasi yang konsisten di seluruh kanal. Tujuan utamanya adalah?',
                        'Memperkuat identitas dan pengenalan merek',
                        'Mengurangi jumlah karyawan',
                        'Mengubah struktur modal',
                        'Menghapus market research',
                    ),
                    $this->question(
                        'Dua produk memiliki fitur hampir sama, tetapi perusahaan ingin mereknya dipersepsikan berbeda dari pesaing. Konsep yang paling relevan adalah?',
                        'Brand differentiation',
                        'Payroll processing',
                        'Debt restructuring',
                        'Candidate scoring',
                    ),
                ],
                'man-digital-marketing' => [
                    $this->question(
                        'Kampanye iklan digital bertujuan menghasilkan pendaftaran. Metrik paling langsung untuk mengevaluasi tujuan tersebut adalah?',
                        'Conversion rate pendaftaran',
                        'Ukuran logo',
                        'Jumlah warna banner',
                        'Jumlah halaman dokumen internal',
                    ),
                    $this->question(
                        'Iklan mendapat 10.000 impresi dan 500 klik. Metrik yang membandingkan klik terhadap impresi adalah?',
                        'Click-through rate',
                        'Current ratio',
                        'Employee turnover',
                        'Inventory days',
                    ),
                    $this->question(
                        'Dua kampanye menghasilkan jumlah pelanggan baru sama, tetapi kampanye A menggunakan biaya lebih kecil. Metrik yang tepat untuk membandingkan efisiensi adalah?',
                        'Cost per acquisition',
                        'Jumlah posting',
                        'Jumlah karyawan',
                        'Current asset ratio',
                    ),
                ],
                'man-market-research' => [
                    $this->question(
                        'Perusahaan ingin mengetahui alasan pelanggan berpindah ke kompetitor. Data paling relevan dikumpulkan melalui?',
                        'Wawancara atau survei pelanggan yang berpindah',
                        'Daftar warna kantor',
                        'Jumlah perangkat karyawan',
                        'Nama file laporan lama',
                    ),
                    $this->question(
                        'Sampel penelitian hanya berasal dari pelanggan paling loyal. Risiko utama metode tersebut adalah?',
                        'Sampling bias',
                        'Brand awareness terlalu tinggi',
                        'Likuiditas perusahaan turun',
                        'Semua data otomatis menjadi kualitatif',
                    ),
                    $this->question(
                        'Data yang dikumpulkan perusahaan sendiri melalui survei pelanggan termasuk?',
                        'Primary data',
                        'Data yang tidak dapat dianalisis',
                        'Data jaringan komputer',
                        'Data akuntansi wajib',
                    ),
                ],
                'man-financial-planning' => [
                    $this->question(
                        'Dalam financial planning, proyeksi arus kas terutama digunakan untuk?',
                        'Memperkirakan kemampuan memenuhi kebutuhan kas pada periode mendatang',
                        'Menentukan warna merek',
                        'Menyusun struktur organisasi',
                        'Menilai kualitas wawancara',
                    ),
                    $this->question(
                        'Perusahaan memperkirakan pemasukan dan pengeluaran untuk dua belas bulan berikutnya. Aktivitas tersebut merupakan bagian dari?',
                        'Budgeting dan financial planning',
                        'Brand positioning',
                        'Job analysis',
                        'Market segmentation',
                    ),
                    $this->question(
                        'Perusahaan membuat skenario optimistis, normal, dan pesimistis untuk proyeksi keuangan. Tujuan utamanya adalah?',
                        'Memahami dampak berbagai kondisi terhadap rencana keuangan',
                        'Menjamin satu skenario pasti terjadi',
                        'Menghapus seluruh risiko',
                        'Menggantikan pencatatan transaksi',
                    ),
                ],
                'man-financial-analysis' => [
                    $this->question(
                        'Analisis tren laporan keuangan dilakukan terutama untuk?',
                        'Melihat perubahan kinerja dan posisi keuangan dari waktu ke waktu',
                        'Menentukan slogan pemasaran',
                        'Menilai desain antarmuka',
                        'Mengatur topologi jaringan',
                    ),
                    $this->question(
                        'Pendapatan naik tetapi laba bersih turun. Analisis berikutnya yang paling relevan adalah?',
                        'Memeriksa perubahan biaya dan margin keuntungan',
                        'Mengganti logo perusahaan',
                        'Mengubah seluruh deskripsi pekerjaan',
                        'Mengabaikan laporan laba rugi',
                    ),
                    $this->question(
                        'Perusahaan memiliki laba positif tetapi arus kas operasi terus negatif. Hal yang paling tepat dilakukan adalah?',
                        'Menganalisis kualitas laba dan pergerakan kas perusahaan',
                        'Menganggap kondisi pasti sehat karena laba positif',
                        'Mengabaikan laporan arus kas',
                        'Mengganti strategi branding',
                    ),
                ],
                'man-investment-management' => [
                    $this->question(
                        'Prinsip dasar hubungan risiko dan imbal hasil dalam investasi adalah?',
                        'Potensi imbal hasil lebih tinggi umumnya disertai risiko lebih tinggi',
                        'Semua investasi memberikan hasil pasti',
                        'Risiko tidak perlu dipertimbangkan',
                        'Diversifikasi selalu menghapus seluruh risiko',
                    ),
                    $this->question(
                        'Investor membagi dana ke beberapa jenis aset dengan karakteristik berbeda. Tujuan utama tindakan tersebut adalah?',
                        'Diversifikasi risiko',
                        'Menghilangkan seluruh risiko investasi',
                        'Menjamin keuntungan tetap',
                        'Menghindari analisis investasi',
                    ),
                    $this->question(
                        'Dua investasi memiliki return yang diperkirakan sama tetapi salah satunya memiliki risiko jauh lebih rendah. Secara umum pilihan yang lebih efisien adalah?',
                        'Investasi dengan risiko lebih rendah setelah asumsi diverifikasi',
                        'Investasi dengan risiko lebih tinggi tanpa alasan',
                        'Memilih secara acak',
                        'Mengabaikan risiko',
                    ),
                ],
                'man-recruitment-selection' => [
                    $this->question(
                        'Sebelum membuka lowongan, langkah rekrutmen yang paling tepat adalah?',
                        'Menetapkan kebutuhan jabatan dan profil kandidat',
                        'Mengiklankan posisi tanpa deskripsi kerja',
                        'Memilih kandidat pertama',
                        'Mengabaikan kebutuhan organisasi',
                    ),
                    $this->question(
                        'Sebelum menentukan kualifikasi kandidat, perusahaan perlu memahami tugas dan tanggung jawab posisi melalui?',
                        'Job analysis',
                        'Brand audit',
                        'Financial forecasting',
                        'Market segmentation',
                    ),
                    $this->question(
                        'Agar proses seleksi kandidat konsisten dan adil, perusahaan sebaiknya?',
                        'Menggunakan kriteria kompetensi dan metode penilaian yang terstruktur',
                        'Mengandalkan intuisi pewawancara saja',
                        'Memilih berdasarkan foto profil',
                        'Mengubah kriteria untuk setiap kandidat',
                    ),
                ],
                'man-performance-management' => [
                    $this->question(
                        'Sasaran kinerja yang baik seharusnya?',
                        'Spesifik, terukur, relevan, dan memiliki batas waktu',
                        'Berubah setiap hari tanpa alasan',
                        'Tidak memiliki indikator',
                        'Hanya diketahui atasan',
                    ),
                    $this->question(
                        'Umpan balik kinerja yang efektif sebaiknya?',
                        'Spesifik, berdasarkan perilaku atau hasil, dan memberikan arah perbaikan',
                        'Hanya diberikan ketika terjadi kesalahan besar',
                        'Berdasarkan rumor',
                        'Tidak dikaitkan dengan sasaran kerja',
                    ),
                    $this->question(
                        'KPI dalam performance management terutama digunakan untuk?',
                        'Mengukur pencapaian terhadap sasaran kinerja yang ditetapkan',
                        'Menggantikan seluruh komunikasi antara atasan dan karyawan',
                        'Menentukan warna identitas perusahaan',
                        'Menghapus kebutuhan evaluasi',
                    ),
                ],
                'man-talent-management' => [
                    $this->question(
                        'Succession planning bertujuan untuk?',
                        'Menyiapkan talenta bagi peran penting di masa depan',
                        'Menghapus semua program pengembangan',
                        'Mengganti seluruh karyawan setiap tahun',
                        'Menghindari evaluasi kompetensi',
                    ),
                    $this->question(
                        'Program pengembangan karyawan berpotensi tinggi terutama bertujuan untuk?',
                        'Mempersiapkan kemampuan mereka bagi tanggung jawab yang lebih besar',
                        'Menghapus seluruh proses evaluasi',
                        'Menghindari succession planning',
                        'Mengurangi seluruh pelatihan',
                    ),
                    $this->question(
                        'Perusahaan kehilangan banyak karyawan berkinerja tinggi. Analisis talent management yang paling relevan adalah?',
                        'Menganalisis faktor retensi, pengembangan, penghargaan, dan peluang karier',
                        'Mengabaikan alasan karyawan keluar',
                        'Menghapus program pengembangan',
                        'Mengganti seluruh proses rekrutmen tanpa analisis',
                    ),
                ],
            ],

            'Teknik Informatika' => [
                'ti-algorithms-data-structures' => [
                    $this->question(
                        'Sebuah algoritma pencarian bekerja pada data yang sudah terurut. Algoritma yang secara umum lebih efisien daripada linear search adalah?',
                        'Binary search',
                        'Bubble sort',
                        'Random search',
                        'Sequential sorting',
                    ),
                    $this->question(
                        'Struktur data yang menerapkan pola First In First Out adalah?',
                        'Queue',
                        'Stack',
                        'Binary tree',
                        'Hash function',
                    ),
                    $this->question(
                        'Kompleksitas waktu binary search pada data terurut adalah?',
                        'O(log n)',
                        'O(n²)',
                        'O(2ⁿ)',
                        'O(n!)',
                    ),
                ],
                'ti-object-oriented-programming' => [
                    $this->question(
                        'Menyembunyikan detail internal object dan menyediakan akses melalui interface terkontrol disebut?',
                        'Encapsulation',
                        'Recursion',
                        'Compilation',
                        'Serialization',
                    ),
                    $this->question(
                        'Kemampuan object dengan tipe dasar sama untuk menjalankan implementasi perilaku berbeda disebut?',
                        'Polymorphism',
                        'Normalization',
                        'Indexing',
                        'Compilation',
                    ),
                    $this->question(
                        'Membuat class baru berdasarkan class yang sudah ada dan mewarisi perilakunya disebut?',
                        'Inheritance',
                        'Aggregation SQL',
                        'Packet routing',
                        'Normalization',
                    ),
                ],
                'ti-software-engineering' => [
                    $this->question(
                        'Requirement berubah ketika pengembangan sudah berjalan. Praktik paling tepat adalah?',
                        'Menganalisis dampak, memperbarui requirement, lalu menyesuaikan implementasi',
                        'Mengabaikan perubahan',
                        'Menghapus seluruh source code',
                        'Menerapkan langsung ke production tanpa tes',
                    ),
                    $this->question(
                        'Tujuan utama automated test dalam pengembangan software adalah?',
                        'Memverifikasi perilaku sistem dan membantu mendeteksi regresi',
                        'Menggantikan seluruh requirement',
                        'Menghilangkan version control',
                        'Menjamin software tidak pernah memiliki bug',
                    ),
                    $this->question(
                        'Version control seperti Git terutama digunakan untuk?',
                        'Mencatat perubahan source code dan membantu kolaborasi',
                        'Menggantikan database production',
                        'Menjalankan sistem operasi',
                        'Mengatur alamat IP jaringan',
                    ),
                ],
                'ti-computer-networks' => [
                    $this->question(
                        'Perangkat akan mengirim paket ke jaringan di luar subnet lokal. Paket biasanya terlebih dahulu diarahkan ke?',
                        'Default gateway',
                        'Loopback address',
                        'Port USB',
                        'Alamat broadcast aplikasi',
                    ),
                    $this->question(
                        'Protokol transport yang menyediakan koneksi, pengurutan paket, dan retransmission adalah?',
                        'TCP',
                        'UDP',
                        'ARP',
                        'ICMP saja',
                    ),
                    $this->question(
                        'Perangkat jaringan yang meneruskan frame berdasarkan MAC address pada LAN adalah?',
                        'Switch',
                        'Printer',
                        'Keyboard',
                        'Power supply',
                    ),
                ],
                'ti-operating-systems' => [
                    $this->question(
                        'Virtual memory memungkinkan sistem operasi untuk?',
                        'Menggunakan penyimpanan sebagai perluasan logis memori utama saat diperlukan',
                        'Menghilangkan kebutuhan CPU',
                        'Menjalankan jaringan tanpa protokol',
                        'Menghapus semua proses',
                    ),
                    $this->question(
                        'Perbedaan umum process dan thread adalah?',
                        'Thread dalam process dapat berbagi ruang memori process yang sama',
                        'Setiap thread memiliki sistem operasi sendiri',
                        'Process tidak memiliki memori',
                        'Thread hanya digunakan untuk jaringan',
                    ),
                    $this->question(
                        'Komponen sistem operasi yang menentukan process berikutnya yang memperoleh waktu CPU adalah?',
                        'Scheduler',
                        'Compiler',
                        'DNS resolver',
                        'Database index',
                    ),
                ],
                'ti-cybersecurity' => [
                    $this->question(
                        'Prinsip least privilege berarti?',
                        'Memberikan hak akses minimum yang diperlukan untuk tugas',
                        'Memberikan administrator kepada semua pengguna',
                        'Menyimpan password sebagai teks biasa',
                        'Menonaktifkan logging',
                    ),
                    $this->question(
                        'Menggunakan password dan kode dari aplikasi authenticator merupakan contoh?',
                        'Multi-factor authentication',
                        'Anonymous access',
                        'Plaintext authentication',
                        'Single factor tanpa password',
                    ),
                    $this->question(
                        'Password pengguna sebaiknya disimpan pada database dengan?',
                        'Password hashing yang kuat',
                        'Plain text',
                        'Nama pengguna sebagai password',
                        'Encoding Base64 sebagai satu-satunya perlindungan',
                    ),
                ],
                'ti-machine-learning' => [
                    $this->question(
                        'Model sangat baik pada data training tetapi buruk pada data baru. Kondisi ini disebut?',
                        'Overfitting',
                        'Underfitting',
                        'Normalization',
                        'Clustering',
                    ),
                    $this->question(
                        'Model yang belajar dari contoh data yang memiliki label termasuk?',
                        'Supervised learning',
                        'Unsupervised learning saja',
                        'Database normalization',
                        'Network routing',
                    ),
                    $this->question(
                        'Tujuan memisahkan training set dan test set adalah?',
                        'Mengevaluasi kemampuan model pada data yang tidak digunakan untuk training',
                        'Menggunakan test set untuk melatih seluruh parameter',
                        'Menghapus kebutuhan evaluasi',
                        'Menjamin model selalu akurat',
                    ),
                ],
                'ti-data-science' => [
                    $this->question(
                        'Dataset memiliki banyak nilai kosong sebelum analisis. Langkah yang paling tepat adalah?',
                        'Menganalisis pola missing value lalu menentukan penanganan yang sesuai',
                        'Mengabaikan kualitas data',
                        'Mengganti semua nilai dengan angka acak',
                        'Menghapus target analisis',
                    ),
                    $this->question(
                        'Menggunakan informasi dari data test ketika membangun fitur training dapat menyebabkan?',
                        'Data leakage',
                        'Database replication',
                        'Network congestion',
                        'UI inconsistency',
                    ),
                    $this->question(
                        'Exploratory Data Analysis terutama dilakukan untuk?',
                        'Memahami pola, distribusi, hubungan, dan anomali pada data',
                        'Menghapus seluruh data sebelum dianalisis',
                        'Menggantikan seluruh proses pengumpulan data',
                        'Menjamin semua hipotesis benar',
                    ),
                ],
                'ti-computer-vision' => [
                    $this->question(
                        'Dalam klasifikasi citra, data augmentation umumnya digunakan untuk?',
                        'Menambah variasi data training secara terkontrol',
                        'Menghapus seluruh label',
                        'Menggantikan evaluasi model',
                        'Mengubah tugas menjadi routing jaringan',
                    ),
                    $this->question(
                        'Tugas Computer Vision yang menentukan lokasi sekaligus kelas beberapa object pada gambar disebut?',
                        'Object detection',
                        'Text sorting',
                        'Database indexing',
                        'Network routing',
                    ),
                    $this->question(
                        'Operasi convolution pada CNN terutama membantu model untuk?',
                        'Mengekstraksi pola lokal seperti edge dan fitur visual',
                        'Membuat query SQL',
                        'Mengatur alamat IP',
                        'Mengelola filesystem',
                    ),
                ],
            ],

            'Sistem Komputer' => [
                'sk-computer-architecture' => [
                    $this->question(
                        'Komponen yang mengeksekusi instruksi dan operasi aritmetika-logika terutama berada pada?',
                        'CPU',
                        'Power supply',
                        'Monitor',
                        'Keyboard',
                    ),
                    $this->question(
                        'Cache memory ditempatkan dekat processor terutama untuk?',
                        'Mengurangi waktu akses terhadap data atau instruksi yang sering digunakan',
                        'Menggantikan seluruh storage permanen',
                        'Menyediakan koneksi internet',
                        'Mengatur resolusi monitor',
                    ),
                    $this->question(
                        'Tahapan dasar instruction cycle secara umum mencakup?',
                        'Fetch, decode, dan execute',
                        'Upload, download, dan print',
                        'Login, logout, dan shutdown',
                        'Encrypt dan delete saja',
                    ),
                ],
                'sk-digital-logic' => [
                    $this->question(
                        'Gerbang AND menghasilkan keluaran 1 ketika?',
                        'Semua input bernilai 1',
                        'Minimal satu input bernilai 1',
                        'Semua input bernilai 0',
                        'Input selalu berbeda',
                    ),
                    $this->question(
                        'Gerbang XOR menghasilkan keluaran 1 ketika?',
                        'Input berbeda satu sama lain',
                        'Semua input selalu 1',
                        'Semua input selalu 0',
                        'Output selalu sama dengan input pertama',
                    ),
                    $this->question(
                        'Komponen digital yang dapat menyimpan satu bit keadaan disebut?',
                        'Flip-flop',
                        'Resistor pasif saja',
                        'Router',
                        'Database index',
                    ),
                ],
                'sk-microprocessor-microcontroller' => [
                    $this->question(
                        'Dibanding microprocessor umum, microcontroller biasanya mengintegrasikan?',
                        'CPU, memory, dan peripheral dalam satu chip',
                        'Hanya monitor dan keyboard',
                        'Hanya hard disk',
                        'Hanya network switch',
                    ),
                    $this->question(
                        'Register pada processor digunakan terutama untuk?',
                        'Menyimpan data atau instruksi sementara yang sedang diproses',
                        'Menyimpan arsip bertahun-tahun',
                        'Menggantikan seluruh RAM',
                        'Menghubungkan monitor ke listrik',
                    ),
                    $this->question(
                        'Microcontroller lebih cocok daripada microprocessor umum untuk banyak perangkat embedded karena?',
                        'Peripheral dan memory terintegrasi serta dirancang untuk kontrol perangkat',
                        'Selalu memiliki GPU lebih cepat',
                        'Tidak memerlukan program',
                        'Tidak menggunakan listrik',
                    ),
                ],
                'sk-embedded-systems' => [
                    $this->question(
                        'Pada embedded system real-time, salah satu kebutuhan penting adalah?',
                        'Respons sistem memenuhi batas waktu yang ditentukan',
                        'Semua proses boleh memiliki waktu tak terbatas',
                        'Tidak memerlukan pengujian',
                        'Harus selalu menggunakan layar besar',
                    ),
                    $this->question(
                        'Software yang berjalan langsung untuk mengendalikan hardware embedded sering disebut?',
                        'Firmware',
                        'Spreadsheet',
                        'Web browser',
                        'Database report',
                    ),
                    $this->question(
                        'Embedded system umumnya dirancang untuk?',
                        'Menjalankan fungsi khusus pada perangkat tertentu',
                        'Menggantikan seluruh internet',
                        'Menjalankan semua jenis pekerjaan tanpa batas',
                        'Menjadi database publik secara otomatis',
                    ),
                ],
                'sk-internet-of-things' => [
                    $this->question(
                        'Protokol ringan yang banyak digunakan untuk publish-subscribe pada perangkat IoT adalah?',
                        'MQTT',
                        'JPEG',
                        'HTML',
                        'CSV',
                    ),
                    $this->question(
                        'Alur IoT yang umum adalah?',
                        'Sensor mengumpulkan data, perangkat memproses atau mengirim data, lalu aplikasi menggunakannya',
                        'Monitor mengirim listrik ke CPU',
                        'Database menggantikan seluruh sensor',
                        'Keyboard menjadi router',
                    ),
                    $this->question(
                        'Saat perangkat IoT dikirim ke internet, praktik keamanan yang penting adalah?',
                        'Menggunakan autentikasi dan komunikasi terenkripsi',
                        'Menggunakan password default selamanya',
                        'Membuka seluruh port tanpa kebutuhan',
                        'Menonaktifkan pembaruan keamanan',
                    ),
                ],
                'sk-sensor-actuator-integration' => [
                    $this->question(
                        'Sensor analog menghasilkan tegangan yang perlu dibaca microcontroller digital. Komponen yang digunakan adalah?',
                        'ADC',
                        'DAC saja',
                        'Router',
                        'GPU',
                    ),
                    $this->question(
                        'Teknik yang umum digunakan microcontroller untuk mengatur kecepatan motor DC secara efisien adalah?',
                        'PWM',
                        'DNS',
                        'SQL JOIN',
                        'JPEG compression',
                    ),
                    $this->question(
                        'Kalibrasi sensor dilakukan terutama untuk?',
                        'Meningkatkan kesesuaian hasil pengukuran terhadap nilai referensi',
                        'Mengubah sensor menjadi actuator',
                        'Menggantikan microcontroller',
                        'Menambah bandwidth internet',
                    ),
                ],
                'sk-computer-networks' => [
                    $this->question(
                        'Perangkat yang meneruskan frame berdasarkan MAC address di jaringan lokal adalah?',
                        'Switch',
                        'Printer',
                        'Microphone',
                        'Power supply',
                    ),
                    $this->question(
                        'Router digunakan terutama untuk?',
                        'Meneruskan packet antar jaringan IP',
                        'Menyimpan dokumen pengguna',
                        'Menggambar antarmuka',
                        'Menggantikan RAM',
                    ),
                    $this->question(
                        'Protokol TCP digunakan ketika aplikasi membutuhkan?',
                        'Pengiriman data yang andal dan berurutan',
                        'Tidak ada kontrol pengiriman sama sekali',
                        'Akses langsung ke sensor analog',
                        'Penyimpanan file lokal',
                    ),
                ],
                'sk-network-administration' => [
                    $this->question(
                        'VLAN digunakan untuk?',
                        'Membagi jaringan logis pada infrastruktur switch yang sama',
                        'Mengubah CPU menjadi lebih cepat',
                        'Menggantikan seluruh firewall',
                        'Menyimpan password pengguna',
                    ),
                    $this->question(
                        'DHCP digunakan untuk?',
                        'Memberikan konfigurasi IP kepada host secara otomatis',
                        'Mengenkripsi seluruh hard disk',
                        'Mengompilasi source code',
                        'Menggambar topologi jaringan',
                    ),
                    $this->question(
                        'Sebelum mengubah konfigurasi jaringan production, administrator sebaiknya?',
                        'Membuat backup konfigurasi dan rencana rollback',
                        'Menghapus seluruh konfigurasi lama',
                        'Menonaktifkan semua monitoring',
                        'Melakukan perubahan tanpa dokumentasi',
                    ),
                ],
                'sk-network-security' => [
                    $this->question(
                        'Enkripsi data selama transmisi terutama bertujuan menjaga?',
                        'Confidentiality',
                        'Ukuran monitor',
                        'Clock speed processor',
                        'Jumlah port USB',
                    ),
                    $this->question(
                        'Firewall digunakan terutama untuk?',
                        'Mengontrol traffic jaringan berdasarkan aturan keamanan',
                        'Menambah kapasitas RAM',
                        'Mengubah resolusi monitor',
                        'Mengganti database',
                    ),
                    $this->question(
                        'Sistem yang menganalisis aktivitas jaringan untuk mendeteksi pola serangan disebut?',
                        'Intrusion Detection System',
                        'Spreadsheet',
                        'Compiler',
                        'Image editor',
                    ),
                ],
            ],

            'Psikologi' => [
                'psi-employee-behavior' => [
                    $this->question(
                        'Karyawan menunjukkan penurunan kepuasan setelah beban kerja meningkat tanpa dukungan tambahan. Faktor yang paling relevan dianalisis adalah?',
                        'Kondisi kerja, beban kerja, dan dukungan organisasi',
                        'Jenis database',
                        'Topologi router',
                        'Ukuran monitor',
                    ),
                    $this->question(
                        'Nilai, norma, dan kebiasaan bersama yang membentuk cara anggota organisasi bertindak disebut?',
                        'Organizational culture',
                        'Network protocol',
                        'Financial ratio',
                        'Database schema',
                    ),
                    $this->question(
                        'Seorang karyawan memiliki kemampuan baik tetapi motivasinya turun setelah merasa kontribusinya tidak dihargai. Faktor yang paling relevan dianalisis adalah?',
                        'Motivasi dan persepsi penghargaan dalam pekerjaan',
                        'Kecepatan jaringan',
                        'Struktur tabel database',
                        'Resolusi layar',
                    ),
                ],
                'psi-organizational-development' => [
                    $this->question(
                        'Perubahan organisasi sering ditolak oleh karyawan. Langkah yang membantu proses perubahan adalah?',
                        'Melibatkan stakeholder dan menjelaskan alasan serta dampak perubahan',
                        'Menyembunyikan seluruh informasi',
                        'Mengabaikan kekhawatiran karyawan',
                        'Mengubah struktur setiap hari',
                    ),
                    $this->question(
                        'Sebelum menentukan intervensi organizational development, organisasi sebaiknya?',
                        'Melakukan diagnosis terhadap masalah dan kebutuhan organisasi',
                        'Langsung menerapkan perubahan tanpa data',
                        'Menghapus seluruh kebijakan',
                        'Menentukan hasil tanpa evaluasi',
                    ),
                    $this->question(
                        'Setelah intervensi pengembangan organisasi dilakukan, langkah penting berikutnya adalah?',
                        'Mengevaluasi dampak intervensi menggunakan indikator yang relevan',
                        'Menganggap intervensi selalu berhasil',
                        'Menghentikan seluruh pengumpulan data',
                        'Mengabaikan feedback anggota organisasi',
                    ),
                ],
                'psi-psychological-assessment' => [
                    $this->question(
                        'Reliabilitas instrumen psikologi merujuk pada?',
                        'Konsistensi hasil pengukuran',
                        'Keindahan tampilan instrumen',
                        'Jumlah halaman instrumen',
                        'Popularitas instrumen',
                    ),
                    $this->question(
                        'Validitas instrumen psikologi berkaitan dengan?',
                        'Sejauh mana instrumen mengukur konstruk yang seharusnya diukur',
                        'Jumlah warna pada formulir',
                        'Ukuran file data',
                        'Kecepatan komputer',
                    ),
                    $this->question(
                        'Hasil psychological assessment paling tepat digunakan dengan cara?',
                        'Diinterpretasikan sesuai tujuan, prosedur, konteks, dan batasan instrumen',
                        'Digunakan sebagai satu-satunya dasar keputusan tanpa konteks',
                        'Dianggap selalu sempurna',
                        'Dibagikan kepada siapa pun tanpa memperhatikan kerahasiaan',
                    ),
                ],
                'psi-counseling-skills' => [
                    $this->question(
                        'Pertanyaan terbuka dalam konseling bermanfaat karena?',
                        'Memberikan ruang kepada klien menjelaskan pengalaman dengan lebih luas',
                        'Membatasi jawaban hanya ya atau tidak',
                        'Memaksa klien menerima nasihat',
                        'Menghilangkan kebutuhan mendengarkan',
                    ),
                    $this->question(
                        'Active listening dalam konseling ditunjukkan dengan?',
                        'Memperhatikan, mengklarifikasi, dan merespons isi pembicaraan secara tepat',
                        'Memotong pembicaraan terus-menerus',
                        'Mengubah topik setiap saat',
                        'Membuat asumsi tanpa klarifikasi',
                    ),
                    $this->question(
                        'Teknik merangkum dalam konseling digunakan untuk?',
                        'Menyatukan poin penting pembicaraan dan memastikan pemahaman',
                        'Mengakhiri pembicaraan tanpa alasan',
                        'Mengubah cerita klien',
                        'Memberikan diagnosis otomatis',
                    ),
                ],
                'psi-interpersonal-communication' => [
                    $this->question(
                        'Active listening dalam komunikasi interpersonal ditunjukkan dengan?',
                        'Memperhatikan, mengklarifikasi, dan memberikan respons yang sesuai',
                        'Memotong pembicaraan',
                        'Mengalihkan topik',
                        'Membuat asumsi tanpa klarifikasi',
                    ),
                    $this->question(
                        'Komunikasi asertif berarti?',
                        'Menyampaikan kebutuhan dan pendapat dengan jelas sambil menghormati orang lain',
                        'Memaksakan kehendak kepada orang lain',
                        'Menghindari seluruh perbedaan pendapat',
                        'Tidak pernah menyampaikan kebutuhan',
                    ),
                    $this->question(
                        'Bahasa tubuh, ekspresi wajah, dan kontak mata termasuk?',
                        'Komunikasi nonverbal',
                        'Analisis statistik',
                        'Network communication',
                        'Database query',
                    ),
                ],
                'psi-emotional-intelligence' => [
                    $this->question(
                        'Kemampuan menahan respons impulsif ketika sedang marah merupakan bagian dari?',
                        'Self-regulation',
                        'Database management',
                        'Market research',
                        'Network administration',
                    ),
                    $this->question(
                        'Kemampuan memahami perasaan orang lain dari sudut pandangnya berkaitan dengan?',
                        'Empathy',
                        'Financial analysis',
                        'Digital logic',
                        'Database indexing',
                    ),
                    $this->question(
                        'Kemampuan mengenali emosi diri sendiri dan pengaruhnya terhadap perilaku disebut?',
                        'Self-awareness',
                        'Routing',
                        'Normalization',
                        'Brand positioning',
                    ),
                ],
                'psi-research-methodology' => [
                    $this->question(
                        'Definisi operasional variabel dibutuhkan agar?',
                        'Konsep penelitian dapat diukur atau diamati secara jelas',
                        'Hipotesis selalu terbukti',
                        'Semua responden memberi jawaban sama',
                        'Analisis statistik tidak diperlukan',
                    ),
                    $this->question(
                        'Hipotesis penelitian pada dasarnya merupakan?',
                        'Pernyataan sementara yang dapat diuji menggunakan data',
                        'Kesimpulan akhir yang tidak dapat diubah',
                        'Daftar responden',
                        'Instrumen penelitian',
                    ),
                    $this->question(
                        'Pemilihan sampel yang sesuai penting karena?',
                        'Mempengaruhi kualitas inferensi terhadap populasi yang diteliti',
                        'Menjamin seluruh hasil selalu benar',
                        'Menghapus kebutuhan metode penelitian',
                        'Menghilangkan seluruh bias secara otomatis',
                    ),
                ],
                'psi-interview-observation' => [
                    $this->question(
                        'Pewawancara hanya mencari informasi yang mendukung dugaan awalnya. Hal tersebut berisiko menimbulkan?',
                        'Confirmation bias',
                        'Random sampling',
                        'Reliabilitas sempurna',
                        'Validitas otomatis',
                    ),
                    $this->question(
                        'Dua observer memberikan skor sangat berbeda untuk perilaku yang sama. Aspek yang perlu ditingkatkan adalah?',
                        'Inter-rater reliability',
                        'Ukuran font',
                        'Jumlah folder penelitian',
                        'Warna lembar observasi',
                    ),
                    $this->question(
                        'Catatan observasi yang baik sebaiknya?',
                        'Membedakan deskripsi perilaku yang diamati dari interpretasi observer',
                        'Hanya berisi opini observer',
                        'Menghilangkan konteks kejadian',
                        'Ditulis berdasarkan ingatan beberapa minggu kemudian saja',
                    ),
                ],
                'psi-survey-data-analysis' => [
                    $this->question(
                        'Skala Likert umumnya digunakan untuk mengukur?',
                        'Tingkat sikap atau persetujuan responden terhadap pernyataan',
                        'Alamat IP responden',
                        'Kecepatan CPU',
                        'Struktur database',
                    ),
                    $this->question(
                        'Analisis deskriptif digunakan terutama untuk?',
                        'Merangkum dan menggambarkan karakteristik data yang diperoleh',
                        'Membuktikan seluruh hubungan bersifat kausal',
                        'Menghilangkan kebutuhan pengumpulan data',
                        'Mengganti metode penelitian',
                    ),
                    $this->question(
                        'Sebelum menganalisis hasil survey, data sebaiknya diperiksa untuk?',
                        'Menemukan data kosong, nilai tidak valid, dan ketidakkonsistenan',
                        'Mengubah seluruh jawaban agar sama',
                        'Menghapus semua responden',
                        'Memastikan hipotesis pasti benar',
                    ),
                ],
            ],

            'Ilmu Komunikasi' => [
                'ikom-media-relations' => [
                    $this->question(
                        'Hubungan media yang baik sebaiknya dibangun melalui?',
                        'Informasi yang akurat, respons profesional, dan hubungan yang konsisten',
                        'Mengirim informasi palsu agar cepat diberitakan',
                        'Menghindari seluruh pertanyaan wartawan',
                        'Memberi informasi berbeda kepada setiap media',
                    ),
                    $this->question(
                        'Press release yang efektif sebaiknya mengutamakan?',
                        'Informasi yang relevan, jelas, faktual, dan memiliki nilai berita',
                        'Bahasa yang sengaja ambigu',
                        'Promosi tanpa fakta',
                        'Informasi yang tidak dapat diverifikasi',
                    ),
                    $this->question(
                        'Sebelum menghubungi media untuk suatu isu, praktisi PR sebaiknya?',
                        'Memahami media, audiensnya, dan relevansi informasi yang akan disampaikan',
                        'Mengirim pesan sama ke semua pihak tanpa konteks',
                        'Mengabaikan kebutuhan informasi wartawan',
                        'Memberi data yang belum diverifikasi',
                    ),
                ],
                'ikom-corporate-communication' => [
                    $this->question(
                        'Sebelum menyusun pesan perusahaan untuk perubahan besar, langkah penting adalah?',
                        'Mengidentifikasi stakeholder dan kebutuhan informasi mereka',
                        'Memilih warna poster terlebih dahulu',
                        'Menghapus komunikasi internal',
                        'Menggunakan pesan sama tanpa mempertimbangkan audiens',
                    ),
                    $this->question(
                        'Corporate communication yang konsisten membantu organisasi dalam?',
                        'Menjaga keselarasan pesan dan reputasi di antara stakeholder',
                        'Menghapus seluruh kritik publik',
                        'Menggantikan strategi bisnis',
                        'Menghilangkan kebutuhan komunikasi internal',
                    ),
                    $this->question(
                        'Karyawan mengetahui perubahan organisasi dari media sebelum perusahaan memberi informasi internal. Masalah utama yang terjadi adalah?',
                        'Koordinasi komunikasi internal yang lemah',
                        'Database terlalu besar',
                        'Kualitas kamera buruk',
                        'Jaringan menggunakan VLAN',
                    ),
                ],
                'ikom-crisis-communication' => [
                    $this->question(
                        'Pesan awal singkat ketika krisis masih diselidiki sering disebut?',
                        'Holding statement',
                        'Balance sheet',
                        'Database migration',
                        'Source code patch',
                    ),
                    $this->question(
                        'Pada awal krisis, komunikasi organisasi sebaiknya mengutamakan?',
                        'Kecepatan, akurasi, empati, dan informasi yang telah diverifikasi',
                        'Spekulasi agar terlihat cepat',
                        'Diam tanpa penilaian kondisi',
                        'Menyalahkan pihak lain sebelum investigasi',
                    ),
                    $this->question(
                        'Menunjuk spokesperson pada situasi krisis membantu organisasi untuk?',
                        'Menjaga konsistensi dan koordinasi pesan kepada publik',
                        'Menghilangkan seluruh risiko krisis',
                        'Menggantikan investigasi',
                        'Mencegah media mengajukan pertanyaan',
                    ),
                ],
                'ikom-news-writing' => [
                    $this->question(
                        'Unsur dasar 5W+1H dalam berita mencakup?',
                        'What, who, when, where, why, dan how',
                        'Width, weight, web, window, write, dan host',
                        'Work, wage, wire, wall, word, dan home',
                        'Who saja',
                    ),
                    $this->question(
                        'Struktur inverted pyramid dalam penulisan berita menempatkan?',
                        'Informasi paling penting pada bagian awal',
                        'Informasi paling penting hanya di akhir',
                        'Opini reporter pada paragraf pertama',
                        'Iklan sebelum fakta utama',
                    ),
                    $this->question(
                        'Lead berita yang baik umumnya bertujuan untuk?',
                        'Menyampaikan informasi terpenting secara ringkas dan menarik',
                        'Menunda seluruh fakta sampai akhir',
                        'Memasukkan seluruh detail dalam satu kalimat panjang',
                        'Menggantikan seluruh isi berita',
                    ),
                ],
                'ikom-journalistic-interview' => [
                    $this->question(
                        'Setelah narasumber memberi jawaban umum, teknik yang tepat untuk memperoleh detail adalah?',
                        'Mengajukan follow-up question yang relevan',
                        'Mengakhiri wawancara langsung',
                        'Mengubah jawaban narasumber sendiri',
                        'Mengabaikan informasi yang belum jelas',
                    ),
                    $this->question(
                        'Pertanyaan terbuka dalam wawancara jurnalistik berguna untuk?',
                        'Mendorong narasumber memberikan penjelasan yang lebih luas',
                        'Membatasi semua jawaban menjadi ya atau tidak',
                        'Memaksa narasumber menyetujui reporter',
                        'Menghilangkan kebutuhan follow-up',
                    ),
                    $this->question(
                        'Sebelum wawancara jurnalistik dilakukan, reporter sebaiknya?',
                        'Melakukan riset topik dan mempersiapkan pertanyaan utama',
                        'Datang tanpa memahami topik',
                        'Menentukan kesimpulan sebelum wawancara',
                        'Mengabaikan latar belakang narasumber',
                    ),
                ],
                'ikom-news-reporting' => [
                    $this->question(
                        'Menggunakan beberapa sumber independen untuk memeriksa informasi yang sama membantu meningkatkan?',
                        'Verifikasi dan kredibilitas laporan',
                        'Jumlah iklan',
                        'Ukuran file video',
                        'Kecepatan komputer',
                    ),
                    $this->question(
                        'Cara paling kuat untuk memverifikasi klaim statistik adalah?',
                        'Menelusuri data atau dokumen sumber primer yang dapat diperiksa',
                        'Mengandalkan jumlah likes',
                        'Mengutip unggahan tanpa sumber',
                        'Menganggap klaim benar karena sering dibagikan',
                    ),
                    $this->question(
                        'Dalam news reporting, reporter perlu membedakan dengan jelas antara?',
                        'Fakta yang terverifikasi dan opini',
                        'Warna dan ukuran font saja',
                        'File lokal dan cloud saja',
                        'Hardware dan software saja',
                    ),
                ],
                'ikom-content-creation' => [
                    $this->question(
                        'Konten digital bertujuan mendorong pengguna mendaftar ke sebuah acara. Elemen penting yang perlu dicantumkan adalah?',
                        'Call to action yang jelas',
                        'Informasi yang sengaja ambigu',
                        'Judul tanpa isi',
                        'Hashtag acak sebanyak mungkin',
                    ),
                    $this->question(
                        'Sebelum membuat konten, creator sebaiknya menentukan?',
                        'Tujuan komunikasi dan audiens yang ingin dijangkau',
                        'Jumlah efek visual sebanyak mungkin',
                        'Format secara acak',
                        'Caption sebelum memahami tujuan',
                    ),
                    $this->question(
                        'Storytelling dalam content creation terutama membantu?',
                        'Menyusun pesan agar lebih mudah dipahami dan memiliki alur yang menarik',
                        'Menggantikan seluruh fakta',
                        'Menghilangkan kebutuhan memahami audiens',
                        'Menjamin seluruh konten viral',
                    ),
                ],
                'ikom-social-media-management' => [
                    $this->question(
                        'Content calendar berguna terutama untuk?',
                        'Merencanakan waktu, topik, format, dan kanal publikasi secara konsisten',
                        'Menggantikan seluruh analisis audiens',
                        'Menentukan password akun',
                        'Menghapus seluruh metrik performa',
                    ),
                    $this->question(
                        'Engagement rate pada media sosial membantu mengukur?',
                        'Interaksi audiens terhadap konten relatif terhadap basis pengukuran yang digunakan',
                        'Jumlah kabel jaringan',
                        'Nilai aset perusahaan',
                        'Kecepatan processor',
                    ),
                    $this->question(
                        'Ketika komentar negatif mulai meningkat, social media manager sebaiknya?',
                        'Memantau konteks, merespons sesuai pedoman, dan mengeskalasi isu serius',
                        'Menghapus seluruh komentar tanpa penilaian',
                        'Membalas secara emosional',
                        'Mengabaikan semua komentar',
                    ),
                ],
                'ikom-video-production' => [
                    $this->question(
                        'Tahap sebelum pengambilan gambar yang mencakup konsep, script, dan shot list disebut?',
                        'Pre-production',
                        'Post-production',
                        'Distribution',
                        'Archiving',
                    ),
                    $this->question(
                        'Shot list terutama digunakan untuk?',
                        'Merencanakan gambar atau shot yang perlu direkam saat produksi',
                        'Mengatur database',
                        'Menghitung rasio keuangan',
                        'Membuat konfigurasi jaringan',
                    ),
                    $this->question(
                        'Dalam produksi video, audio dialog yang jelas penting karena?',
                        'Membantu audiens memahami pesan utama video',
                        'Audio tidak memengaruhi pengalaman penonton',
                        'Audio hanya dibutuhkan untuk video tanpa gambar',
                        'Kualitas audio selalu dapat diabaikan',
                    ),
                ],
            ],
        ];

        $assessments = Assessment::query()
            ->whereIn(
                'study_program',
                array_keys(
                    AcademicAssessmentCatalog::programs(),
                ),
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
            AcademicAssessmentCatalog::programs() as $studyProgram => $expectedSkillSlugs
        ) {
            $assessment = $assessments->get(
                $studyProgram,
            );

            if (! $assessment) {
                continue;
            }

            $questionsBySkill = $sets[
                $studyProgram
            ] ?? null;

            if (
                ! is_array($questionsBySkill)
                || array_keys($questionsBySkill)
                    !== $expectedSkillSlugs
            ) {
                throw new RuntimeException(
                    'Definisi bank soal '.$studyProgram.' tidak sesuai dengan katalog Assesment.',
                );
            }

            $targetSkillIds = [];

            foreach ($expectedSkillSlugs as $skillSlug) {
                $skill = $skills->get(
                    $skillSlug,
                );

                if (! $skill) {
                    throw new RuntimeException(
                        'Skill '.$skillSlug.' tidak ditemukan.',
                    );
                }

                $targetSkillIds[] = $skill->id;
            }

            $obsoleteQuestionIds = $assessment
                ->questions()
                ->whereNotIn(
                    'skill_id',
                    $targetSkillIds,
                )
                ->pluck('id')
                ->map(
                    fn ($id) => (int) $id,
                )
                ->values()
                ->all();

            $this->deleteUnusedQuestions(
                $obsoleteQuestionIds,
                $studyProgram,
            );

            foreach (
                $questionsBySkill as $skillSlug => $questions
            ) {
                $skill = $skills->get(
                    $skillSlug,
                );

                if (! $skill) {
                    throw new RuntimeException(
                        'Skill '.$skillSlug.' tidak ditemukan.',
                    );
                }

                $existingQuestions = $assessment
                    ->questions()
                    ->where(
                        'skill_id',
                        $skill->id,
                    )
                    ->orderBy('id')
                    ->get();

                foreach (
                    $questions as $questionIndex => $definition
                ) {
                    $prompt = $definition[
                        'prompt'
                    ];

                    $options = $definition[
                        'options'
                    ];

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

                    $existingQuestion = $existingQuestions->get(
                        $questionIndex,
                    );

                    if ($existingQuestion) {
                        $existingQuestion->update(
                            $attributes,
                        );

                        continue;
                    }

                    $assessment
                        ->questions()
                        ->create(
                            $attributes,
                        );
                }

                $extraQuestionIds = $existingQuestions
                    ->slice(
                        AcademicAssessmentCatalog::QUESTIONS_PER_SKILL,
                    )
                    ->pluck('id')
                    ->map(
                        fn ($id) => (int) $id,
                    )
                    ->values()
                    ->all();

                $this->deleteUnusedQuestions(
                    $extraQuestionIds,
                    $studyProgram,
                );
            }

            $questionCount = $assessment
                ->questions()
                ->count();

            if (
                $questionCount
                !== AcademicAssessmentCatalog::QUESTION_POOL_SIZE
            ) {
                throw new RuntimeException(
                    'Bank soal '.$studyProgram.' harus memiliki tepat 27 soal, tetapi ditemukan '.$questionCount.'.',
                );
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

    /**
     * @param  array<int, int>  $questionIds
     */
    private function deleteUnusedQuestions(
        array $questionIds,
        string $studyProgram,
    ): void {
        if ($questionIds === []) {
            return;
        }

        $hasHistoricalResults = AssessmentResult::query()
            ->whereIn(
                'assessment_question_id',
                $questionIds,
            )
            ->exists();

        if ($hasHistoricalResults) {
            throw new RuntimeException(
                'Bank soal '.$studyProgram.' memiliki soal lama yang masih digunakan oleh histori Assesment. Soal tersebut tidak dihapus agar histori pengguna tidak rusak.',
            );
        }

        AssessmentQuestion::query()
            ->whereIn(
                'id',
                $questionIds,
            )
            ->delete();
    }
}
