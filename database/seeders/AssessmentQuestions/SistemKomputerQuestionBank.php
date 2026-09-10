<?php

namespace Database\Seeders\AssessmentQuestions;

final class SistemKomputerQuestionBank implements AssessmentQuestionBank
{
    public static function studyProgram(): string
    {
        return 'Sistem Komputer';
    }

    public static function questions(): array
    {
        return [
            'sk-computer-architecture' => [
                [
                    'Komponen yang mengeksekusi instruksi dan operasi aritmetika-logika terutama berada pada?',
                    'CPU',
                    'Power supply',
                    'Monitor',
                    'Keyboard',
                ],
                [
                    'Cache memory ditempatkan dekat processor terutama untuk?',
                    'Mengurangi waktu akses terhadap data atau instruksi yang sering digunakan',
                    'Menggantikan seluruh storage permanen',
                    'Menyediakan koneksi internet',
                    'Mengatur resolusi monitor',
                ],
                [
                    'Tahapan dasar instruction cycle secara umum mencakup?',
                    'Fetch, decode, dan execute',
                    'Upload, download, dan print',
                    'Login, logout, dan shutdown',
                    'Encrypt dan delete saja',
                ],
                [
                    'Pada arsitektur komputer, tujuan utama cache memory yang berada dekat dengan prosesor adalah?',
                    'Mengurangi waktu rata-rata akses terhadap data dan instruksi yang sering digunakan',
                    'Menggantikan seluruh fungsi penyimpanan permanen',
                    'Menghapus kebutuhan terhadap register',
                    'Mengubah sinyal digital menjadi analog',
                ],
            ],
            'sk-digital-logic' => [
                [
                    'Gerbang AND menghasilkan keluaran 1 ketika?',
                    'Semua input bernilai 1',
                    'Minimal satu input bernilai 1',
                    'Semua input bernilai 0',
                    'Input selalu berbeda',
                ],
                [
                    'Gerbang XOR menghasilkan keluaran 1 ketika?',
                    'Input berbeda satu sama lain',
                    'Semua input selalu 1',
                    'Semua input selalu 0',
                    'Output selalu sama dengan input pertama',
                ],
                [
                    'Komponen digital yang dapat menyimpan satu bit keadaan disebut?',
                    'Flip-flop',
                    'Resistor pasif saja',
                    'Router',
                    'Database index',
                ],
            ],
            'sk-microprocessor-microcontroller' => [
                [
                    'Dibanding microprocessor umum, microcontroller biasanya mengintegrasikan?',
                    'CPU, memory, dan peripheral dalam satu chip',
                    'Hanya monitor dan keyboard',
                    'Hanya hard disk',
                    'Hanya network switch',
                ],
                [
                    'Register pada processor digunakan terutama untuk?',
                    'Menyimpan data atau instruksi sementara yang sedang diproses',
                    'Menyimpan arsip bertahun-tahun',
                    'Menggantikan seluruh RAM',
                    'Menghubungkan monitor ke listrik',
                ],
                [
                    'Microcontroller lebih cocok daripada microprocessor umum untuk banyak perangkat embedded karena?',
                    'Peripheral dan memory terintegrasi serta dirancang untuk kontrol perangkat',
                    'Selalu memiliki GPU lebih cepat',
                    'Tidak memerlukan program',
                    'Tidak menggunakan listrik',
                ],
            ],
            'sk-embedded-systems' => [
                [
                    'Pada embedded system real-time, salah satu kebutuhan penting adalah?',
                    'Respons sistem memenuhi batas waktu yang ditentukan',
                    'Semua proses boleh memiliki waktu tak terbatas',
                    'Tidak memerlukan pengujian',
                    'Harus selalu menggunakan layar besar',
                ],
                [
                    'Software yang berjalan langsung untuk mengendalikan hardware embedded sering disebut?',
                    'Firmware',
                    'Spreadsheet',
                    'Web browser',
                    'Database report',
                ],
                [
                    'Embedded system umumnya dirancang untuk?',
                    'Menjalankan fungsi khusus pada perangkat tertentu',
                    'Menggantikan seluruh internet',
                    'Menjalankan semua jenis pekerjaan tanpa batas',
                    'Menjadi database publik secara otomatis',
                ],
                [
                    'Sebuah embedded system harus dapat pulih ketika program utama berhenti merespons. Mekanisme yang paling tepat digunakan adalah?',
                    'Watchdog timer',
                    'CSS media query',
                    'Database trigger',
                    'DNS resolver',
                ],
            ],
            'sk-internet-of-things' => [
                [
                    'Protokol ringan yang banyak digunakan untuk publish-subscribe pada perangkat IoT adalah?',
                    'MQTT',
                    'JPEG',
                    'HTML',
                    'CSV',
                ],
                [
                    'Alur IoT yang umum adalah?',
                    'Sensor mengumpulkan data, perangkat memproses atau mengirim data, lalu aplikasi menggunakannya',
                    'Monitor mengirim listrik ke CPU',
                    'Database menggantikan seluruh sensor',
                    'Keyboard menjadi router',
                ],
                [
                    'Saat perangkat IoT dikirim ke internet, praktik keamanan yang penting adalah?',
                    'Menggunakan autentikasi dan komunikasi terenkripsi',
                    'Menggunakan password default selamanya',
                    'Membuka seluruh port tanpa kebutuhan',
                    'Menonaktifkan pembaruan keamanan',
                ],
            ],
            'sk-sensor-actuator-integration' => [
                [
                    'Sensor analog menghasilkan tegangan yang perlu dibaca microcontroller digital. Komponen yang digunakan adalah?',
                    'ADC',
                    'DAC saja',
                    'Router',
                    'GPU',
                ],
                [
                    'Teknik yang umum digunakan microcontroller untuk mengatur kecepatan motor DC secara efisien adalah?',
                    'PWM',
                    'DNS',
                    'SQL JOIN',
                    'JPEG compression',
                ],
                [
                    'Kalibrasi sensor dilakukan terutama untuk?',
                    'Meningkatkan kesesuaian hasil pengukuran terhadap nilai referensi',
                    'Mengubah sensor menjadi actuator',
                    'Menggantikan microcontroller',
                    'Menambah bandwidth internet',
                ],
            ],
            'sk-computer-networks' => [
                [
                    'Perangkat yang meneruskan frame berdasarkan MAC address di jaringan lokal adalah?',
                    'Switch',
                    'Printer',
                    'Microphone',
                    'Power supply',
                ],
                [
                    'Router digunakan terutama untuk?',
                    'Meneruskan packet antar jaringan IP',
                    'Menyimpan dokumen pengguna',
                    'Menggambar antarmuka',
                    'Menggantikan RAM',
                ],
                [
                    'Protokol TCP digunakan ketika aplikasi membutuhkan?',
                    'Pengiriman data yang andal dan berurutan',
                    'Tidak ada kontrol pengiriman sama sekali',
                    'Akses langsung ke sensor analog',
                    'Penyimpanan file lokal',
                ],
                [
                    'Sebuah komputer akan mengirim paket ke jaringan yang berbeda dari subnet lokalnya. Perangkat atau alamat yang digunakan sebagai tujuan awal paket tersebut adalah?',
                    'Default gateway',
                    'Loopback address',
                    'Broadcast lokal sebagai tujuan akhir',
                    'Alamat MAC komputer itu sendiri',
                ],
            ],
            'sk-network-administration' => [
                [
                    'VLAN digunakan untuk?',
                    'Membagi jaringan logis pada infrastruktur switch yang sama',
                    'Mengubah CPU menjadi lebih cepat',
                    'Menggantikan seluruh firewall',
                    'Menyimpan password pengguna',
                ],
                [
                    'DHCP digunakan untuk?',
                    'Memberikan konfigurasi IP kepada host secara otomatis',
                    'Mengenkripsi seluruh hard disk',
                    'Mengompilasi source code',
                    'Menggambar topologi jaringan',
                ],
                [
                    'Sebelum mengubah konfigurasi jaringan production, administrator sebaiknya?',
                    'Membuat backup konfigurasi dan rencana rollback',
                    'Menghapus seluruh konfigurasi lama',
                    'Menonaktifkan semua monitoring',
                    'Melakukan perubahan tanpa dokumentasi',
                ],
            ],
            'sk-network-security' => [
                [
                    'Enkripsi data selama transmisi terutama bertujuan menjaga?',
                    'Confidentiality',
                    'Ukuran monitor',
                    'Clock speed processor',
                    'Jumlah port USB',
                ],
                [
                    'Firewall digunakan terutama untuk?',
                    'Mengontrol traffic jaringan berdasarkan aturan keamanan',
                    'Menambah kapasitas RAM',
                    'Mengubah resolusi monitor',
                    'Mengganti database',
                ],
                [
                    'Sistem yang menganalisis aktivitas jaringan untuk mendeteksi pola serangan disebut?',
                    'Intrusion Detection System',
                    'Spreadsheet',
                    'Compiler',
                    'Image editor',
                ],
            ],
        ];
    }
}
