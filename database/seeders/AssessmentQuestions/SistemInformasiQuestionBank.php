<?php

namespace Database\Seeders\AssessmentQuestions;

final class SistemInformasiQuestionBank implements AssessmentQuestionBank
{
    public static function studyProgram(): string
    {
        return 'Sistem Informasi';
    }

    public static function questions(): array
    {
        return [
            'si-sql-data-processing' => [
                [
                    'Sebuah tabel transaksi memiliki customer_id dan total. Query yang paling tepat untuk menghitung total belanja setiap customer adalah?',
                    'SELECT customer_id, SUM(total) FROM transaksi GROUP BY customer_id',
                    'SELECT * FROM transaksi ORDER BY customer_id',
                    'DELETE FROM transaksi WHERE customer_id IS NOT NULL',
                    'ALTER TABLE transaksi ADD total_belanja INT',
                ],
                [
                    'Sebuah laporan hanya boleh menampilkan pelanggan yang total transaksinya lebih dari Rp10 juta setelah data dikelompokkan. Klausa SQL yang tepat adalah?',
                    'HAVING',
                    'ORDER BY',
                    'ALTER TABLE',
                    'DROP TABLE',
                ],
                [
                    'Anda ingin menampilkan seluruh pelanggan termasuk pelanggan yang belum pernah melakukan transaksi. Jenis JOIN yang paling tepat adalah?',
                    'LEFT JOIN dari tabel pelanggan ke tabel transaksi',
                    'INNER JOIN yang hanya mengambil data yang cocok',
                    'CROSS JOIN seluruh data',
                    'Tidak menggunakan JOIN sama sekali',
                ],
                [
                    'Sebuah tabel transaksi menyimpan banyak transaksi dari pelanggan yang sama. Ekspresi SQL yang paling tepat untuk menghitung jumlah pelanggan unik adalah?',
                    'COUNT(DISTINCT customer_id)',
                    'COUNT(*)',
                    'SUM(customer_id)',
                    'ORDER BY customer_id',
                ],
            ],
            'si-spreadsheet-data-analysis' => [
                [
                    'Anda memiliki ribuan baris penjualan dan ingin merangkum total per kategori tanpa menulis rumus satu per satu. Fitur spreadsheet yang paling tepat adalah?',
                    'Pivot Table',
                    'Freeze Panes',
                    'Conditional Formatting',
                    'Protect Sheet',
                ],
                [
                    'Dua tabel spreadsheet memiliki kode produk yang sama. Anda ingin mengambil nama produk dari tabel referensi berdasarkan kode tersebut. Fitur yang paling tepat adalah?',
                    'XLOOKUP atau fungsi lookup sejenis',
                    'Freeze Panes',
                    'Conditional Formatting',
                    'Protect Sheet',
                ],
                [
                    'Anda ingin menjumlahkan nilai penjualan hanya ketika wilayah dan kategori memenuhi kriteria tertentu. Fungsi spreadsheet yang paling sesuai adalah?',
                    'SUMIFS',
                    'LEFT',
                    'LEN',
                    'CONCAT',
                ],
            ],
            'si-business-intelligence-data-visualization' => [
                [
                    'Dashboard manajemen ingin memantau pencapaian penjualan terhadap target bulanan. Komponen BI yang paling relevan adalah?',
                    'KPI yang menampilkan aktual, target, dan selisih',
                    'Seluruh transaksi mentah tanpa ringkasan',
                    'Dokumen naratif tanpa metrik',
                    'Tabel tanpa periode waktu',
                ],
                [
                    'Manajemen ingin melihat penjualan berdasarkan waktu, wilayah, dan kategori produk secara interaktif. Pendekatan paling tepat adalah?',
                    'Membuat dashboard dengan dimensi, metrik, dan filter yang relevan',
                    'Menampilkan seluruh data mentah tanpa agregasi',
                    'Menyimpan laporan hanya dalam file teks',
                    'Menghapus data historis',
                ],
                [
                    'Visualisasi yang paling tepat untuk menunjukkan perubahan nilai penjualan dari Januari sampai Desember adalah?',
                    'Line chart',
                    'Pie chart tanpa dimensi waktu',
                    'Tabel tanpa urutan bulan',
                    'Diagram yang tidak memiliki sumbu waktu',
                ],
            ],
            'si-database-management' => [
                [
                    'Dalam database relasional, foreign key terutama digunakan untuk?',
                    'Menghubungkan data antar tabel dan menjaga integritas referensial',
                    'Mengubah semua kolom menjadi teks',
                    'Menggantikan semua primary key',
                    'Membuat antarmuka pengguna',
                ],
                [
                    'Pemindahan saldo harus berhasil seluruhnya atau dibatalkan seluruhnya jika salah satu proses gagal. Konsep database yang paling relevan adalah?',
                    'Transaction dan atomicity',
                    'CSS inheritance',
                    'HTTP caching',
                    'Image compression',
                ],
                [
                    'Kolom email sering digunakan pada kondisi WHERE dan pencarian mulai lambat ketika data bertambah besar. Optimasi database yang paling relevan adalah?',
                    'Membuat index yang sesuai pada kolom tersebut',
                    'Menghapus primary key',
                    'Mengubah seluruh tabel menjadi satu kolom',
                    'Menonaktifkan constraint',
                ],
                [
                    'Kolom email pada tabel pengguna tidak boleh memiliki nilai yang sama untuk dua akun berbeda. Constraint database yang paling tepat adalah?',
                    'UNIQUE constraint',
                    'DEFAULT constraint',
                    'CHECK yang selalu bernilai benar',
                    'INDEX biasa tanpa aturan unik',
                ],
            ],
            'si-web-development' => [
                [
                    'Frontend perlu mengambil daftar produk dari backend tanpa memuat ulang seluruh halaman. Pendekatan yang paling tepat adalah?',
                    'Memanggil endpoint API melalui HTTP',
                    'Menulis data produk di CSS',
                    'Menjalankan query database langsung dari browser',
                    'Menyimpan seluruh data di gambar',
                ],
                [
                    'Sebuah endpoint berhasil membuat resource baru pada server. Status HTTP yang paling sesuai adalah?',
                    '201 Created',
                    '404 Not Found',
                    '401 Unauthorized',
                    '500 Internal Server Error',
                ],
                [
                    'Data formulir dari browser akan disimpan ke database. Praktik backend yang paling tepat sebelum penyimpanan adalah?',
                    'Melakukan validasi input pada server',
                    'Mempercayai seluruh input dari browser',
                    'Menghapus seluruh aturan validasi',
                    'Menyimpan input sebelum diperiksa',
                ],
            ],
            'si-system-analysis-design' => [
                [
                    'Sebelum membangun sistem baru, proses bisnis tiap divisi ternyata berbeda. Langkah awal analis sistem yang paling tepat adalah?',
                    'Menggali kebutuhan dan memetakan proses yang sedang berjalan',
                    'Langsung memilih framework',
                    'Langsung membuat database production',
                    'Menghapus proses lama tanpa analisis',
                ],
                [
                    'Analis ingin mengetahui aktor dan interaksi utama pengguna dengan sistem sebelum detail teknis dibuat. Diagram yang paling sesuai adalah?',
                    'Use case diagram',
                    'Pie chart',
                    'Network topology',
                    'Bar chart',
                ],
                [
                    'Pernyataan "sistem harus memungkinkan pelanggan mengubah alamat pengiriman" termasuk jenis requirement?',
                    'Functional requirement',
                    'Hardware specification',
                    'Network topology',
                    'Visual branding guideline',
                ],
            ],
            'si-ui-design' => [
                [
                    'Tombol utama dan tombol sekunder memiliki tampilan sama kuat sehingga pengguna bingung. Prinsip UI yang perlu diperbaiki adalah?',
                    'Hierarki visual',
                    'Normalisasi database',
                    'Routing jaringan',
                    'Version control',
                ],
                [
                    'Teks abu-abu muda pada latar putih sulit dibaca. Aspek UI yang paling perlu diperbaiki adalah?',
                    'Kontras visual dan keterbacaan',
                    'Konfigurasi DNS',
                    'Algoritma sorting',
                    'Normalisasi database',
                ],
                [
                    'Komponen yang memiliki fungsi sama terlihat berbeda pada setiap halaman. Prinsip desain yang perlu diperbaiki adalah?',
                    'Konsistensi antarmuka',
                    'Database replication',
                    'Packet routing',
                    'Memory allocation',
                ],
                [
                    'Pengguna salah mengisi format email pada formulir. Respons antarmuka yang paling membantu adalah?',
                    'Menampilkan pesan kesalahan yang spesifik di dekat field email',
                    'Menghapus seluruh isi formulir tanpa penjelasan',
                    'Menyembunyikan field email',
                    'Mengubah warna seluruh halaman tanpa pesan',
                ],
            ],
            'si-wireframing-prototyping' => [
                [
                    'Tujuan utama wireframe pada tahap awal desain adalah?',
                    'Memvalidasi struktur halaman dan prioritas konten sebelum detail visual',
                    'Menentukan password database',
                    'Mengukur bandwidth',
                    'Menentukan versi compiler',
                ],
                [
                    'Tim ingin mencoba beberapa struktur halaman dengan cepat tanpa fokus pada warna dan ilustrasi. Artefak yang paling tepat adalah?',
                    'Low-fidelity wireframe',
                    'Database production',
                    'Final design system lengkap',
                    'Server monitoring dashboard',
                ],
                [
                    'Sebelum fitur dikembangkan, tim ingin mengetahui apakah pengguna memahami alur pemesanan. Prototype sebaiknya digunakan untuk?',
                    'Menguji alur dan interaksi dengan pengguna',
                    'Menggantikan seluruh backend',
                    'Menyimpan transaksi production',
                    'Mengatur firewall server',
                ],
            ],
            'si-user-research' => [
                [
                    'Tim ingin memahami kebutuhan dan hambatan pengguna sebelum merancang fitur. Metode yang paling tepat adalah?',
                    'Wawancara atau observasi pengguna yang relevan',
                    'Menebak kebutuhan berdasarkan preferensi tim',
                    'Menyalin fitur kompetitor tanpa riset',
                    'Mengubah desain secara acak',
                ],
                [
                    'Tim ingin mengetahui alasan pengguna berhenti pada tahap tertentu dalam aplikasi. Metode yang paling membantu adalah?',
                    'Wawancara pengguna disertai observasi perilaku',
                    'Menebak berdasarkan pendapat developer',
                    'Menghapus analytics',
                    'Mengubah seluruh produk tanpa riset',
                ],
                [
                    'Setelah wawancara beberapa pengguna, tim memiliki banyak catatan temuan. Langkah yang tepat untuk menemukan pola adalah?',
                    'Mengelompokkan temuan berdasarkan tema dan masalah yang berulang',
                    'Memilih satu komentar secara acak',
                    'Mengabaikan data yang berbeda',
                    'Menghapus seluruh catatan wawancara',
                ],
            ],
        ];
    }
}
