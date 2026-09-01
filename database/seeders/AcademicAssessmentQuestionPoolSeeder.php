<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class AcademicAssessmentQuestionPoolSeeder extends Seeder
{
    public function run(): void
    {
        $sets = [
            'Sistem Informasi' => [
                $this->question(
                    'si-sql-data-processing',
                    'Sebuah laporan harus menampilkan hanya customer yang memiliki total transaksi lebih dari Rp10 juta setelah data dikelompokkan. Klausa SQL yang paling tepat digunakan adalah?',
                    'HAVING',
                    'ORDER BY',
                    'ALTER TABLE',
                    'DROP TABLE',
                ),
                $this->question(
                    'si-spreadsheet-data-analysis',
                    'Dua tabel spreadsheet memiliki kolom kode produk yang sama. Anda ingin mengambil nama produk dari tabel referensi berdasarkan kode tersebut. Fitur yang paling sesuai adalah?',
                    'XLOOKUP atau fungsi lookup sejenis',
                    'Freeze Panes',
                    'Conditional Formatting',
                    'Protect Sheet',
                ),
                $this->question(
                    'si-business-intelligence-data-visualization',
                    'Manajemen ingin melihat penjualan berdasarkan waktu, wilayah, dan kategori produk secara interaktif. Pendekatan Business Intelligence yang paling tepat adalah?',
                    'Membuat dashboard dengan dimensi, metrik, dan filter yang relevan',
                    'Menampilkan seluruh data mentah tanpa agregasi',
                    'Menyimpan laporan hanya dalam file teks',
                    'Menghapus data historis agar dashboard lebih sederhana',
                ),
                $this->question(
                    'si-data-visualization',
                    'Anda ingin menunjukkan perubahan jumlah pengguna aktif dari Januari sampai Desember. Visualisasi yang paling tepat adalah?',
                    'Line chart',
                    'Pie chart',
                    'Treemap tanpa dimensi waktu',
                    'Tabel tanpa urutan bulan',
                ),
                $this->question(
                    'si-scenario-based-data-analysis',
                    'Conversion rate turun setelah perubahan halaman checkout. Analisis paling masuk akal dilakukan dengan?',
                    'Membandingkan data sebelum dan sesudah perubahan serta memeriksa tiap tahap funnel',
                    'Menghapus data sebelum perubahan',
                    'Menyimpulkan penyebab hanya dari satu komentar pengguna',
                    'Mengganti seluruh aplikasi tanpa mengukur dampak',
                ),
                $this->question(
                    'si-database-management',
                    'Sebuah transaksi pemindahan saldo harus berhasil seluruhnya atau dibatalkan seluruhnya ketika salah satu proses gagal. Konsep database yang paling relevan adalah?',
                    'Transaction dan atomicity',
                    'CSS inheritance',
                    'HTTP caching',
                    'Image compression',
                ),
                $this->question(
                    'si-web-development',
                    'Sebuah endpoint berhasil membuat resource baru pada server. Status HTTP yang paling sesuai adalah?',
                    '201 Created',
                    '404 Not Found',
                    '401 Unauthorized',
                    '500 Internal Server Error',
                ),
                $this->question(
                    'si-system-analysis-design',
                    'Analis ingin mengetahui aktor dan interaksi utama antara pengguna dengan sistem sebelum detail teknis dibuat. Diagram yang paling sesuai adalah?',
                    'Use case diagram',
                    'ERD untuk struktur tabel saja',
                    'Pie chart',
                    'Network topology',
                ),
                $this->question(
                    'si-erd-uml',
                    'Satu pelanggan dapat memiliki banyak pesanan, sedangkan setiap pesanan hanya dimiliki satu pelanggan. Kardinalitas relasinya adalah?',
                    'One-to-many',
                    'One-to-one',
                    'Many-to-many tanpa tabel penghubung',
                    'Tidak memiliki relasi',
                ),
                $this->question(
                    'si-problem-solving',
                    'Setelah memperbaiki bug, tindakan yang paling penting sebelum menutup masalah adalah?',
                    'Menguji kembali skenario gagal dan memastikan perbaikan tidak menimbulkan regresi',
                    'Menghapus seluruh log',
                    'Mengabaikan test karena kode sudah berubah',
                    'Mengubah komponen lain yang tidak berkaitan',
                ),
                $this->question(
                    'si-ui-design',
                    'Teks abu-abu muda pada latar putih sulit dibaca. Aspek UI yang paling perlu diperbaiki adalah?',
                    'Kontras visual dan keterbacaan',
                    'Normalisasi tabel database',
                    'Konfigurasi DNS',
                    'Algoritma sorting',
                ),
                $this->question(
                    'si-wireframing-prototyping',
                    'Pada tahap eksplorasi awal, tim ingin cepat mencoba beberapa struktur halaman tanpa fokus pada warna dan ilustrasi. Artefak yang paling tepat adalah?',
                    'Low-fidelity wireframe',
                    'Database production',
                    'Final design system lengkap',
                    'Server monitoring dashboard',
                ),
                $this->question(
                    'si-prototyping',
                    'Sebelum fitur dikembangkan, tim ingin mengetahui apakah pengguna memahami alur pemesanan. Prototype sebaiknya digunakan untuk?',
                    'Menguji alur interaksi dengan pengguna',
                    'Menggantikan seluruh backend',
                    'Menyimpan transaksi asli',
                    'Mengatur firewall server',
                ),
                $this->question(
                    'si-user-research',
                    'Tim ingin mengetahui alasan pengguna berhenti pada tahap tertentu dalam aplikasi. Metode yang paling membantu memahami alasannya adalah?',
                    'Wawancara pengguna disertai observasi perilaku',
                    'Menebak berdasarkan pendapat developer',
                    'Mengubah desain secara acak',
                    'Menghapus analytics',
                ),
                $this->question(
                    'si-usability',
                    'Dalam usability testing, metrik yang berguna untuk melihat apakah pengguna dapat menyelesaikan tugas utama adalah?',
                    'Task completion rate',
                    'Jumlah file source code',
                    'Jumlah tabel database',
                    'Kecepatan clock CPU',
                ),
            ],

            'Manajemen' => [
                $this->question(
                    'man-branding',
                    'Sebuah perusahaan menggunakan pesan, warna, dan gaya komunikasi yang konsisten di seluruh kanal. Tujuan utama konsistensi tersebut adalah?',
                    'Memperkuat identitas dan pengenalan merek',
                    'Mengurangi jumlah karyawan',
                    'Mengubah struktur modal',
                    'Menghapus kebutuhan market research',
                ),
                $this->question(
                    'man-digital-marketing',
                    'Iklan digital mendapat 10.000 impresi dan 500 klik. Metrik yang digunakan untuk membandingkan klik terhadap impresi adalah?',
                    'Click-through rate',
                    'Current ratio',
                    'Employee turnover',
                    'Inventory days',
                ),
                $this->question(
                    'man-market-research',
                    'Sampel penelitian pasar hanya berasal dari pelanggan paling loyal. Risiko utama dari metode tersebut adalah?',
                    'Sampling bias',
                    'Brand awareness terlalu tinggi',
                    'Likuiditas perusahaan turun otomatis',
                    'Semua data menjadi kualitatif',
                ),
                $this->question(
                    'man-marketing-strategy',
                    'Proses membagi pasar menjadi kelompok, memilih kelompok yang dituju, lalu menentukan posisi merek dikenal sebagai?',
                    'Segmentation, targeting, dan positioning',
                    'Recruitment, selection, dan onboarding',
                    'Budgeting, auditing, dan taxation',
                    'Planning, coding, dan testing',
                ),
                $this->question(
                    'man-campaign-analysis',
                    'Dua kampanye menghasilkan jumlah conversion sama, tetapi kampanye A menggunakan biaya jauh lebih kecil. Metrik yang membantu membandingkan efisiensi biaya adalah?',
                    'Cost per acquisition',
                    'Jumlah warna iklan',
                    'Jumlah pegawai',
                    'Current asset ratio',
                ),
                $this->question(
                    'man-financial-planning',
                    'Perusahaan memperkirakan pemasukan dan pengeluaran untuk dua belas bulan berikutnya. Aktivitas tersebut merupakan bagian dari?',
                    'Budgeting dan financial planning',
                    'Brand positioning',
                    'Job analysis',
                    'Market segmentation',
                ),
                $this->question(
                    'man-financial-analysis',
                    'Pendapatan naik tetapi laba bersih turun. Analisis berikutnya yang paling relevan adalah?',
                    'Memeriksa perubahan biaya dan margin keuntungan',
                    'Mengganti logo perusahaan',
                    'Mengubah seluruh deskripsi pekerjaan',
                    'Mengabaikan laporan laba rugi',
                ),
                $this->question(
                    'man-financial-ratios',
                    'Rasio debt-to-equity terutama membantu menganalisis?',
                    'Struktur pembiayaan dan tingkat leverage perusahaan',
                    'Engagement media sosial',
                    'Kualitas desain iklan',
                    'Kecepatan proses rekrutmen',
                ),
                $this->question(
                    'man-investment-management',
                    'Investor membagi dana ke beberapa jenis aset dengan karakteristik berbeda. Tujuan utama tindakan tersebut adalah?',
                    'Diversifikasi risiko',
                    'Menghilangkan seluruh risiko investasi',
                    'Menjamin keuntungan tetap',
                    'Menghindari analisis investasi',
                ),
                $this->question(
                    'man-financial-decision-making',
                    'Sebuah proyek memiliki nilai sekarang manfaat yang lebih tinggi daripada seluruh biaya investasinya. Secara umum kondisi ini menunjukkan?',
                    'Proyek memiliki potensi kelayakan finansial',
                    'Proyek pasti mengalami kerugian',
                    'Biaya tidak perlu diperhitungkan',
                    'Risiko dapat diabaikan sepenuhnya',
                ),
                $this->question(
                    'man-recruitment-selection',
                    'Sebelum menentukan kualifikasi kandidat, perusahaan perlu memahami tugas dan tanggung jawab posisi melalui?',
                    'Job analysis',
                    'Brand audit',
                    'Financial forecasting',
                    'Market segmentation',
                ),
                $this->question(
                    'man-candidate-selection',
                    'Dua pewawancara menilai kandidat dengan standar yang sangat berbeda. Cara meningkatkan konsistensi seleksi adalah?',
                    'Menggunakan rubrik dan kriteria penilaian yang sama',
                    'Membiarkan setiap pewawancara membuat aturan sendiri',
                    'Menghapus seluruh catatan interview',
                    'Menilai kandidat berdasarkan kesan pertama saja',
                ),
                $this->question(
                    'man-interview',
                    'Dalam metode STAR, huruf T merujuk pada?',
                    'Task',
                    'Target market',
                    'Turnover',
                    'Technology',
                ),
                $this->question(
                    'man-performance-management',
                    'Umpan balik kinerja yang efektif sebaiknya?',
                    'Spesifik, berdasarkan perilaku atau hasil, dan memberikan arah perbaikan',
                    'Hanya diberikan ketika terjadi kesalahan besar',
                    'Berdasarkan rumor',
                    'Tidak dikaitkan dengan sasaran kerja',
                ),
                $this->question(
                    'man-talent-management',
                    'Program pengembangan karyawan berpotensi tinggi terutama bertujuan untuk?',
                    'Mempersiapkan kemampuan mereka bagi tanggung jawab yang lebih besar',
                    'Menghapus seluruh proses evaluasi',
                    'Menghindari succession planning',
                    'Mengurangi seluruh kegiatan pelatihan',
                ),
            ],

            'Teknik Informatika' => [
                $this->question(
                    'ti-algorithms-data-structures',
                    'Binary search pada data terurut memiliki kompleksitas waktu rata-rata?',
                    'O(log n)',
                    'O(n²)',
                    'O(2ⁿ)',
                    'O(n!)',
                ),
                $this->question(
                    'ti-data-structures',
                    'Struktur data yang menerapkan pola First In First Out adalah?',
                    'Queue',
                    'Stack',
                    'Binary tree',
                    'Hash function',
                ),
                $this->question(
                    'ti-object-oriented-programming',
                    'Kemampuan object dengan tipe dasar yang sama untuk memberikan implementasi perilaku berbeda disebut?',
                    'Polymorphism',
                    'Compilation',
                    'Normalization',
                    'Indexing',
                ),
                $this->question(
                    'ti-software-engineering',
                    'Tujuan utama automated test dalam pengembangan software adalah?',
                    'Memverifikasi perilaku sistem dan membantu mendeteksi regresi',
                    'Menggantikan seluruh requirement',
                    'Menghilangkan kebutuhan version control',
                    'Menjamin software tidak pernah memiliki bug',
                ),
                $this->question(
                    'ti-debugging',
                    'Ketika aplikasi melempar exception, informasi yang paling membantu mengetahui jalur pemanggilan fungsi adalah?',
                    'Stack trace',
                    'Warna editor',
                    'Resolusi monitor',
                    'Nama folder pengguna',
                ),
                $this->question(
                    'ti-computer-networks',
                    'Protokol transport yang menyediakan koneksi, pengurutan paket, dan retransmission adalah?',
                    'TCP',
                    'UDP',
                    'ARP',
                    'ICMP Echo saja',
                ),
                $this->question(
                    'ti-operating-systems',
                    'Perbedaan umum process dan thread adalah?',
                    'Thread dalam process berbagi ruang memori process yang sama',
                    'Setiap thread selalu memiliki sistem operasi sendiri',
                    'Process tidak memiliki memori',
                    'Thread hanya dapat digunakan untuk jaringan',
                ),
                $this->question(
                    'ti-network-troubleshooting',
                    'Sebuah host dapat mengakses website melalui alamat IP tetapi tidak melalui nama domain. Penyebab yang paling mungkin adalah?',
                    'Masalah resolusi DNS',
                    'Kerusakan keyboard',
                    'RAM selalu penuh',
                    'Monitor tidak terdeteksi',
                ),
                $this->question(
                    'ti-cybersecurity',
                    'Menggunakan password dan kode dari aplikasi authenticator merupakan contoh?',
                    'Multi-factor authentication',
                    'Single sign-on tanpa autentikasi',
                    'Plaintext authentication',
                    'Anonymous access',
                ),
                $this->question(
                    'ti-system-administration',
                    'Sebelum melakukan perubahan konfigurasi besar pada server, praktik yang paling aman adalah?',
                    'Membuat backup dan rencana rollback',
                    'Menghapus semua log',
                    'Menonaktifkan seluruh monitoring',
                    'Melakukan perubahan langsung tanpa dokumentasi',
                ),
                $this->question(
                    'ti-machine-learning',
                    'Model yang belajar dari contoh data dengan label termasuk jenis pembelajaran?',
                    'Supervised learning',
                    'Unsupervised learning saja',
                    'Random routing',
                    'Database normalization',
                ),
                $this->question(
                    'ti-data-science',
                    'Menggunakan data test untuk menentukan parameter model sebelum evaluasi akhir dapat menyebabkan?',
                    'Data leakage dan evaluasi yang terlalu optimistis',
                    'Peningkatan integritas database',
                    'Normalisasi jaringan',
                    'Enkripsi otomatis',
                ),
                $this->question(
                    'ti-statistics',
                    'Ukuran yang menunjukkan seberapa tersebar nilai data terhadap rata-ratanya adalah?',
                    'Standard deviation',
                    'Primary key',
                    'HTTP status',
                    'Packet loss',
                ),
                $this->question(
                    'ti-model-evaluation',
                    'Jika kesalahan melewatkan kasus positif sangat berbahaya, metrik yang perlu mendapat perhatian besar adalah?',
                    'Recall',
                    'File size',
                    'Training filename',
                    'Jumlah kolom',
                ),
                $this->question(
                    'ti-computer-vision',
                    'Tugas Computer Vision yang menentukan lokasi sekaligus kelas beberapa objek pada gambar disebut?',
                    'Object detection',
                    'Text sorting',
                    'Database indexing',
                    'Network routing',
                ),
            ],

            'Sistem Komputer' => [
                $this->question(
                    'sk-computer-architecture',
                    'Cache memory ditempatkan dekat processor terutama untuk?',
                    'Mengurangi waktu akses terhadap data atau instruksi yang sering digunakan',
                    'Menggantikan seluruh penyimpanan permanen',
                    'Menyediakan koneksi internet',
                    'Mengatur resolusi monitor',
                ),
                $this->question(
                    'sk-digital-logic',
                    'Gerbang XOR menghasilkan keluaran 1 ketika?',
                    'Input berbeda satu sama lain',
                    'Semua input selalu 1',
                    'Semua input selalu 0',
                    'Minimal satu input selalu 0 tanpa syarat lain',
                ),
                $this->question(
                    'sk-processor',
                    'Tahapan dasar instruction cycle secara umum mencakup?',
                    'Fetch, decode, dan execute',
                    'Upload, download, dan print',
                    'Login, logout, dan shutdown',
                    'Encrypt, compress, dan delete saja',
                ),
                $this->question(
                    'sk-memory',
                    'Jenis memory yang kehilangan isi ketika daya dimatikan adalah?',
                    'RAM',
                    'ROM',
                    'Flash storage',
                    'SSD',
                ),
                $this->question(
                    'sk-microprocessor-microcontroller',
                    'Dibanding microprocessor umum, microcontroller biasanya mengintegrasikan?',
                    'CPU, memory, dan peripheral dalam satu chip',
                    'Hanya monitor dan keyboard',
                    'Hanya hard disk',
                    'Hanya network switch',
                ),
                $this->question(
                    'sk-microcontroller',
                    'Pin GPIO pada microcontroller digunakan terutama untuk?',
                    'Membaca atau menghasilkan sinyal digital',
                    'Membuat query SQL',
                    'Mengubah format video',
                    'Menyimpan website publik',
                ),
                $this->question(
                    'sk-embedded-systems',
                    'Pada embedded system real-time, salah satu kebutuhan penting adalah?',
                    'Respons sistem memenuhi batas waktu yang ditentukan',
                    'Semua proses boleh memiliki waktu tak terbatas',
                    'Tidak memerlukan pengujian',
                    'Harus selalu menggunakan layar besar',
                ),
                $this->question(
                    'sk-internet-of-things',
                    'Protokol ringan yang banyak digunakan untuk publish-subscribe pada perangkat IoT adalah?',
                    'MQTT',
                    'JPEG',
                    'HTML',
                    'SSH key format',
                ),
                $this->question(
                    'sk-sensor-actuator-integration',
                    'Sensor analog menghasilkan tegangan yang perlu dibaca microcontroller digital. Komponen yang digunakan adalah?',
                    'ADC',
                    'DAC saja',
                    'Router',
                    'GPU',
                ),
                $this->question(
                    'sk-actuator',
                    'Teknik yang umum digunakan microcontroller untuk mengatur kecepatan motor DC secara efisien adalah?',
                    'PWM',
                    'DNS',
                    'SQL JOIN',
                    'JPEG compression',
                ),
                $this->question(
                    'sk-computer-networks',
                    'Perangkat yang meneruskan frame berdasarkan MAC address di jaringan lokal adalah?',
                    'Switch',
                    'Printer',
                    'Microphone',
                    'Power supply',
                ),
                $this->question(
                    'sk-network-administration',
                    'VLAN digunakan untuk?',
                    'Membagi jaringan logis pada infrastruktur switch yang sama',
                    'Mengubah CPU menjadi lebih cepat',
                    'Menggantikan seluruh firewall',
                    'Menyimpan password pengguna',
                ),
                $this->question(
                    'sk-network-security',
                    'Enkripsi data selama transmisi terutama bertujuan menjaga?',
                    'Confidentiality',
                    'Ukuran monitor',
                    'Clock speed processor',
                    'Jumlah port USB',
                ),
                $this->question(
                    'sk-firewall',
                    'Stateful firewall berbeda dari filtering stateless karena dapat?',
                    'Melacak keadaan atau konteks koneksi',
                    'Menambah kapasitas hard disk',
                    'Mengubah aplikasi menjadi database',
                    'Menghilangkan seluruh kebutuhan autentikasi',
                ),
                $this->question(
                    'sk-threat-detection',
                    'Sistem yang menganalisis aktivitas jaringan untuk mendeteksi pola serangan disebut?',
                    'Intrusion Detection System',
                    'Spreadsheet',
                    'Compiler',
                    'Image editor',
                ),
            ],

            'Psikologi' => [
                $this->question(
                    'psi-employee-behavior',
                    'Karyawan menunjukkan penurunan kepuasan setelah beban kerja meningkat tanpa dukungan tambahan. Faktor yang paling relevan dianalisis adalah?',
                    'Kondisi kerja, beban kerja, dan dukungan organisasi',
                    'Jenis database',
                    'Topologi router',
                    'Ukuran monitor',
                ),
                $this->question(
                    'psi-organizational-behavior',
                    'Nilai, norma, dan kebiasaan bersama yang membentuk cara anggota organisasi bertindak disebut?',
                    'Organizational culture',
                    'Network protocol',
                    'Financial ratio',
                    'Database schema',
                ),
                $this->question(
                    'psi-work-style-assessment',
                    'Hasil work-style assessment paling tepat digunakan sebagai?',
                    'Salah satu informasi untuk memahami preferensi kerja dan kolaborasi',
                    'Diagnosis klinis tunggal',
                    'Dasar mutlak menentukan nilai seseorang',
                    'Pengganti seluruh observasi dan wawancara',
                ),
                $this->question(
                    'psi-psychological-assessment',
                    'Reliabilitas instrumen psikologi merujuk pada?',
                    'Konsistensi hasil pengukuran',
                    'Keindahan tampilan instrumen',
                    'Jumlah halaman instrumen',
                    'Popularitas instrumen',
                ),
                $this->question(
                    'psi-organizational-development',
                    'Perubahan organisasi sering ditolak oleh karyawan. Langkah yang membantu proses perubahan adalah?',
                    'Melibatkan stakeholder dan menjelaskan alasan serta dampak perubahan',
                    'Menyembunyikan seluruh informasi',
                    'Mengabaikan kekhawatiran karyawan',
                    'Mengubah struktur setiap hari',
                ),
                $this->question(
                    'psi-interpersonal-communication',
                    'Active listening dalam komunikasi interpersonal ditunjukkan dengan?',
                    'Memperhatikan, mengklarifikasi, dan memberikan respons yang sesuai',
                    'Memotong pembicaraan terus-menerus',
                    'Mengalihkan topik ketika lawan bicara berbicara',
                    'Membuat asumsi tanpa klarifikasi',
                ),
                $this->question(
                    'psi-counseling-skills',
                    'Pertanyaan terbuka dalam konseling bermanfaat karena?',
                    'Memberikan ruang kepada klien untuk menjelaskan pengalaman dengan lebih luas',
                    'Membatasi jawaban hanya ya atau tidak',
                    'Memaksa klien menerima nasihat',
                    'Menghilangkan kebutuhan mendengarkan',
                ),
                $this->question(
                    'psi-empathy',
                    'Seorang klien berkata bahwa ia sangat kecewa setelah gagal. Respons empatik paling tepat adalah?',
                    'Mengakui perasaannya dan mencoba memahami pengalaman dari sudut pandangnya',
                    'Mengatakan bahwa masalahnya tidak penting',
                    'Langsung membandingkan kegagalannya dengan orang lain',
                    'Mengubah topik pembicaraan',
                ),
                $this->question(
                    'psi-emotional-intelligence',
                    'Kemampuan menahan respons impulsif ketika sedang marah merupakan bagian dari?',
                    'Self-regulation',
                    'Database management',
                    'Market research',
                    'Network administration',
                ),
                $this->question(
                    'psi-counseling-scenario',
                    'Ketika masalah klien berada di luar kompetensi konselor, tindakan profesional yang tepat adalah?',
                    'Melakukan rujukan kepada pihak yang memiliki kompetensi sesuai',
                    'Berpura-pura menguasai seluruh masalah',
                    'Mengabaikan risiko klien',
                    'Memberikan diagnosis tanpa dasar',
                ),
                $this->question(
                    'psi-research-methodology',
                    'Definisi operasional variabel dibutuhkan agar?',
                    'Konsep penelitian dapat diukur atau diamati secara jelas',
                    'Hipotesis selalu terbukti',
                    'Semua responden memberi jawaban sama',
                    'Analisis statistik tidak diperlukan',
                ),
                $this->question(
                    'psi-interview-observation',
                    'Pewawancara hanya mencari informasi yang mendukung dugaan awalnya. Hal tersebut berisiko menimbulkan?',
                    'Confirmation bias',
                    'Random sampling',
                    'Reliabilitas sempurna',
                    'Validitas otomatis',
                ),
                $this->question(
                    'psi-observation',
                    'Dua observer memberikan skor sangat berbeda untuk perilaku yang sama. Aspek yang perlu ditingkatkan adalah?',
                    'Inter-rater reliability',
                    'Ukuran sampel saja',
                    'Warna lembar observasi',
                    'Jumlah folder penelitian',
                ),
                $this->question(
                    'psi-survey-data-analysis',
                    'Skala Likert umumnya digunakan untuk mengukur?',
                    'Tingkat sikap atau persetujuan responden terhadap pernyataan',
                    'Alamat IP responden',
                    'Kecepatan CPU',
                    'Struktur database',
                ),
                $this->question(
                    'psi-data-analysis',
                    'Analisis deskriptif digunakan terutama untuk?',
                    'Merangkum dan menggambarkan karakteristik data yang diperoleh',
                    'Membuktikan seluruh hubungan bersifat kausal',
                    'Menghilangkan kebutuhan pengumpulan data',
                    'Mengganti metode penelitian',
                ),
            ],

            'Ilmu Komunikasi' => [
                $this->question(
                    'ikom-media-relations',
                    'Hubungan media yang baik sebaiknya dibangun melalui?',
                    'Informasi yang akurat, respons profesional, dan hubungan yang konsisten',
                    'Mengirim informasi palsu agar cepat diberitakan',
                    'Menghindari seluruh pertanyaan wartawan',
                    'Memberi informasi berbeda kepada setiap media',
                ),
                $this->question(
                    'ikom-corporate-communication',
                    'Sebelum menyusun pesan perusahaan untuk suatu perubahan besar, langkah penting adalah?',
                    'Mengidentifikasi stakeholder dan kebutuhan informasi mereka',
                    'Memilih warna poster terlebih dahulu',
                    'Menghapus komunikasi internal',
                    'Menggunakan pesan sama tanpa mempertimbangkan audiens',
                ),
                $this->question(
                    'ikom-crisis-communication',
                    'Pesan awal singkat ketika krisis masih diselidiki sering disebut?',
                    'Holding statement',
                    'Balance sheet',
                    'Database migration',
                    'Source code patch',
                ),
                $this->question(
                    'ikom-public-communication',
                    'Ketika berbicara kepada audiens nonteknis, komunikator sebaiknya?',
                    'Menyesuaikan bahasa dan contoh dengan tingkat pemahaman audiens',
                    'Menggunakan jargon sebanyak mungkin',
                    'Mengabaikan konteks audiens',
                    'Menyampaikan data tanpa struktur',
                ),
                $this->question(
                    'ikom-reputation-management',
                    'Untuk memantau reputasi digital organisasi, data yang relevan antara lain?',
                    'Sentimen percakapan, pemberitaan, keluhan, dan respons publik',
                    'Jumlah kabel jaringan kantor',
                    'Jenis processor komputer',
                    'Jumlah tabel database',
                ),
                $this->question(
                    'ikom-news-writing',
                    'Unsur dasar 5W+1H dalam berita mencakup?',
                    'What, who, when, where, why, dan how',
                    'Width, weight, web, window, write, dan host',
                    'Work, wage, wire, wall, word, dan home',
                    'Who saja',
                ),
                $this->question(
                    'ikom-journalistic-interview',
                    'Setelah narasumber memberi jawaban umum, teknik yang tepat untuk memperoleh detail tambahan adalah?',
                    'Mengajukan follow-up question yang relevan',
                    'Mengakhiri wawancara langsung',
                    'Mengubah jawaban narasumber sendiri',
                    'Mengabaikan informasi yang belum jelas',
                ),
                $this->question(
                    'ikom-news-reporting',
                    'Menggunakan beberapa sumber independen untuk memeriksa informasi yang sama membantu meningkatkan?',
                    'Verifikasi dan kredibilitas laporan',
                    'Jumlah iklan',
                    'Ukuran file video',
                    'Kecepatan komputer',
                ),
                $this->question(
                    'ikom-fact-checking',
                    'Cara paling kuat untuk memverifikasi sebuah klaim statistik adalah?',
                    'Menelusuri data atau dokumen sumber primer yang dapat diperiksa',
                    'Mengandalkan jumlah likes',
                    'Mengutip unggahan tanpa sumber',
                    'Menganggap klaim benar karena sering dibagikan',
                ),
                $this->question(
                    'ikom-journalistic-ethics',
                    'Reporter memiliki hubungan pribadi dengan pihak yang sedang diliput. Situasi tersebut dapat menimbulkan?',
                    'Conflict of interest',
                    'Data normalization',
                    'Packet loss',
                    'Market segmentation',
                ),
                $this->question(
                    'ikom-content-creation',
                    'Konten digital bertujuan mendorong pengguna mendaftar ke suatu acara. Elemen yang penting dicantumkan adalah?',
                    'Call to action yang jelas',
                    'Informasi yang sengaja ambigu',
                    'Judul tanpa isi',
                    'Hashtag acak sebanyak mungkin',
                ),
                $this->question(
                    'ikom-social-media-management',
                    'Content calendar berguna terutama untuk?',
                    'Merencanakan waktu, topik, format, dan kanal publikasi secara konsisten',
                    'Menggantikan seluruh analisis audiens',
                    'Menentukan password akun',
                    'Menghapus seluruh metrik performa',
                ),
                $this->question(
                    'ikom-video-production',
                    'Tahap sebelum proses pengambilan gambar yang mencakup perencanaan konsep, script, dan shot list disebut?',
                    'Pre-production',
                    'Post-production',
                    'Distribution',
                    'Archiving',
                ),
                $this->question(
                    'ikom-content-strategy',
                    'Editorial plan sebaiknya dibuat berdasarkan?',
                    'Tujuan komunikasi, kebutuhan audiens, kanal, dan tema konten',
                    'Tren acak tanpa tujuan',
                    'Selera satu anggota tim saja',
                    'Jumlah posting sebanyak mungkin tanpa metrik',
                ),
                $this->question(
                    'ikom-audience-analysis',
                    'Audience persona digunakan untuk?',
                    'Merangkum karakteristik, kebutuhan, perilaku, dan tujuan kelompok audiens',
                    'Menggantikan seluruh data riset',
                    'Menentukan konfigurasi server',
                    'Menghitung rasio keuangan',
                ),
            ],
        ];

        $assessments = Assessment::query()
            ->whereIn(
                'study_program',
                array_keys($sets),
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
            $sets as $studyProgram => $questions
        ) {
            $assessment = $assessments->get(
                $studyProgram,
            );

            if (! $assessment) {
                continue;
            }

            foreach ($questions as $question) {
                $skillSlug = $question[
                    'skill_slug'
                ];

                $skill = $skills->get(
                    $skillSlug,
                );

                if (! $skill) {
                    continue;
                }

                $prompt = $question[
                    'prompt'
                ];

                $options = $question[
                    'options'
                ];

                $optionA = $options[0];
                $optionB = $options[1];
                $optionC = $options[2];
                $optionD = $options[3];

                $shift = abs(
                    crc32(
                        $skillSlug
                            .'|'
                            .$prompt,
                    ),
                ) % 4;

                $preparedAnswer = $this->answerForIndex(
                    (4 - $shift) % 4,
                );

                AssessmentQuestion::updateOrCreate(
                    [
                        'assessment_id' => $assessment->id,
                        'prompt' => $prompt,
                    ],
                    [
                        'skill_id' => $skill->id,
                        'question_type' => 'multiple_choice',
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
                        'explanation' => 'Jawaban dinilai berdasarkan pemahaman konsep pada skill '.$skill->name.'.',
                        'difficulty' => $skill->difficulty,
                    ],
                );
            }
        }
    }

    /**
     * @return array{
     *     skill_slug: string,
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
        string $skillSlug,
        string $prompt,
        string $correctOption,
        string $wrongOptionOne,
        string $wrongOptionTwo,
        string $wrongOptionThree,
    ): array {
        return [
            'skill_slug' => $skillSlug,
            'prompt' => $prompt,
            'options' => [
                $correctOption,
                $wrongOptionOne,
                $wrongOptionTwo,
                $wrongOptionThree,
            ],
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
