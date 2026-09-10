<?php

namespace Database\Seeders\AssessmentQuestions;

final class TeknikInformatikaQuestionBank implements AssessmentQuestionBank
{
    public static function studyProgram(): string
    {
        return 'Teknik Informatika';
    }

    public static function questions(): array
    {
        return [
            'ti-algorithms-data-structures' => [
                [
                    'Sebuah algoritma pencarian bekerja pada data yang sudah terurut. Algoritma yang secara umum lebih efisien daripada linear search adalah?',
                    'Binary search',
                    'Bubble sort',
                    'Random search',
                    'Sequential sorting',
                ],
                [
                    'Struktur data yang menerapkan pola First In First Out adalah?',
                    'Queue',
                    'Stack',
                    'Binary tree',
                    'Hash function',
                ],
                [
                    'Kompleksitas waktu binary search pada data terurut adalah?',
                    'O(log n)',
                    'O(n²)',
                    'O(2ⁿ)',
                    'O(n!)',
                ],
                [
                    'Binary search dapat digunakan secara benar dan efisien ketika?',
                    'Data sudah terurut berdasarkan nilai yang dicari',
                    'Data harus selalu berbentuk linked list acak',
                    'Semua elemen harus memiliki nilai yang sama',
                    'Data harus dihapus sebelum pencarian',
                ],
            ],
            'ti-object-oriented-programming' => [
                [
                    'Menyembunyikan detail internal object dan menyediakan akses melalui interface terkontrol disebut?',
                    'Encapsulation',
                    'Recursion',
                    'Compilation',
                    'Serialization',
                ],
                [
                    'Kemampuan object dengan tipe dasar sama untuk menjalankan implementasi perilaku berbeda disebut?',
                    'Polymorphism',
                    'Normalization',
                    'Indexing',
                    'Compilation',
                ],
                [
                    'Membuat class baru berdasarkan class yang sudah ada dan mewarisi perilakunya disebut?',
                    'Inheritance',
                    'Aggregation SQL',
                    'Packet routing',
                    'Normalization',
                ],
            ],
            'ti-software-engineering' => [
                [
                    'Requirement berubah ketika pengembangan sudah berjalan. Praktik paling tepat adalah?',
                    'Menganalisis dampak, memperbarui requirement, lalu menyesuaikan implementasi',
                    'Mengabaikan perubahan',
                    'Menghapus seluruh source code',
                    'Menerapkan langsung ke production tanpa tes',
                ],
                [
                    'Tujuan utama automated test dalam pengembangan software adalah?',
                    'Memverifikasi perilaku sistem dan membantu mendeteksi regresi',
                    'Menggantikan seluruh requirement',
                    'Menghilangkan version control',
                    'Menjamin software tidak pernah memiliki bug',
                ],
                [
                    'Version control seperti Git terutama digunakan untuk?',
                    'Mencatat perubahan source code dan membantu kolaborasi',
                    'Menggantikan database production',
                    'Menjalankan sistem operasi',
                    'Mengatur alamat IP jaringan',
                ],
            ],
            'ti-computer-networks' => [
                [
                    'Perangkat akan mengirim paket ke jaringan di luar subnet lokal. Paket biasanya terlebih dahulu diarahkan ke?',
                    'Default gateway',
                    'Loopback address',
                    'Port USB',
                    'Alamat broadcast aplikasi',
                ],
                [
                    'Protokol transport yang menyediakan koneksi, pengurutan paket, dan retransmission adalah?',
                    'TCP',
                    'UDP',
                    'ARP',
                    'ICMP saja',
                ],
                [
                    'Perangkat jaringan yang meneruskan frame berdasarkan MAC address pada LAN adalah?',
                    'Switch',
                    'Printer',
                    'Keyboard',
                    'Power supply',
                ],
                [
                    'Ketika pengguna mengetik nama domain tetapi aplikasi membutuhkan alamat IP server tujuan, layanan jaringan yang bertugas melakukan pemetaan tersebut adalah?',
                    'DNS',
                    'DHCP',
                    'FTP',
                    'SSH',
                ],
            ],
            'ti-operating-systems' => [
                [
                    'Virtual memory memungkinkan sistem operasi untuk?',
                    'Menggunakan penyimpanan sebagai perluasan logis memori utama saat diperlukan',
                    'Menghilangkan kebutuhan CPU',
                    'Menjalankan jaringan tanpa protokol',
                    'Menghapus semua proses',
                ],
                [
                    'Perbedaan umum process dan thread adalah?',
                    'Thread dalam process dapat berbagi ruang memori process yang sama',
                    'Setiap thread memiliki sistem operasi sendiri',
                    'Process tidak memiliki memori',
                    'Thread hanya digunakan untuk jaringan',
                ],
                [
                    'Komponen sistem operasi yang menentukan process berikutnya yang memperoleh waktu CPU adalah?',
                    'Scheduler',
                    'Compiler',
                    'DNS resolver',
                    'Database index',
                ],
            ],
            'ti-cybersecurity' => [
                [
                    'Prinsip least privilege berarti?',
                    'Memberikan hak akses minimum yang diperlukan untuk tugas',
                    'Memberikan administrator kepada semua pengguna',
                    'Menyimpan password sebagai teks biasa',
                    'Menonaktifkan logging',
                ],
                [
                    'Menggunakan password dan kode dari aplikasi authenticator merupakan contoh?',
                    'Multi-factor authentication',
                    'Anonymous access',
                    'Plaintext authentication',
                    'Single factor tanpa password',
                ],
                [
                    'Password pengguna sebaiknya disimpan pada database dengan?',
                    'Password hashing yang kuat',
                    'Plain text',
                    'Nama pengguna sebagai password',
                    'Encoding Base64 sebagai satu-satunya perlindungan',
                ],
            ],
            'ti-machine-learning' => [
                [
                    'Model sangat baik pada data training tetapi buruk pada data baru. Kondisi ini disebut?',
                    'Overfitting',
                    'Underfitting',
                    'Normalization',
                    'Clustering',
                ],
                [
                    'Model yang belajar dari contoh data yang memiliki label termasuk?',
                    'Supervised learning',
                    'Unsupervised learning saja',
                    'Database normalization',
                    'Network routing',
                ],
                [
                    'Tujuan memisahkan training set dan test set adalah?',
                    'Mengevaluasi kemampuan model pada data yang tidak digunakan untuk training',
                    'Menggunakan test set untuk melatih seluruh parameter',
                    'Menghapus kebutuhan evaluasi',
                    'Menjamin model selalu akurat',
                ],
                [
                    'Model memiliki akurasi sangat tinggi pada data training tetapi jauh lebih rendah pada data validation. Kondisi tersebut paling menunjukkan?',
                    'Overfitting',
                    'Normalisasi database',
                    'Packet loss',
                    'Deadlock pada operating system',
                ],
            ],
            'ti-data-science' => [
                [
                    'Dataset memiliki banyak nilai kosong sebelum analisis. Langkah yang paling tepat adalah?',
                    'Menganalisis pola missing value lalu menentukan penanganan yang sesuai',
                    'Mengabaikan kualitas data',
                    'Mengganti semua nilai dengan angka acak',
                    'Menghapus target analisis',
                ],
                [
                    'Menggunakan informasi dari data test ketika membangun fitur training dapat menyebabkan?',
                    'Data leakage',
                    'Database replication',
                    'Network congestion',
                    'UI inconsistency',
                ],
                [
                    'Exploratory Data Analysis terutama dilakukan untuk?',
                    'Memahami pola, distribusi, hubungan, dan anomali pada data',
                    'Menghapus seluruh data sebelum dianalisis',
                    'Menggantikan seluruh proses pengumpulan data',
                    'Menjamin semua hipotesis benar',
                ],
            ],
            'ti-computer-vision' => [
                [
                    'Dalam klasifikasi citra, data augmentation umumnya digunakan untuk?',
                    'Menambah variasi data training secara terkontrol',
                    'Menghapus seluruh label',
                    'Menggantikan evaluasi model',
                    'Mengubah tugas menjadi routing jaringan',
                ],
                [
                    'Tugas Computer Vision yang menentukan lokasi sekaligus kelas beberapa object pada gambar disebut?',
                    'Object detection',
                    'Text sorting',
                    'Database indexing',
                    'Network routing',
                ],
                [
                    'Operasi convolution pada CNN terutama membantu model untuk?',
                    'Mengekstraksi pola lokal seperti edge dan fitur visual',
                    'Membuat query SQL',
                    'Mengatur alamat IP',
                    'Mengelola filesystem',
                ],
            ],
        ];
    }
}
