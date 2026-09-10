<?php

namespace Database\Seeders\AssessmentQuestions;

final class IlmuKomunikasiQuestionBank implements AssessmentQuestionBank
{
    public static function studyProgram(): string
    {
        return 'Ilmu Komunikasi';
    }

    public static function questions(): array
    {
        return [
            'ikom-media-relations' => [
                [
                    'Hubungan media yang baik sebaiknya dibangun melalui?',
                    'Informasi yang akurat, respons profesional, dan hubungan yang konsisten',
                    'Mengirim informasi palsu agar cepat diberitakan',
                    'Menghindari seluruh pertanyaan wartawan',
                    'Memberi informasi berbeda kepada setiap media',
                ],
                [
                    'Press release yang efektif sebaiknya mengutamakan?',
                    'Informasi yang relevan, jelas, faktual, dan memiliki nilai berita',
                    'Bahasa yang sengaja ambigu',
                    'Promosi tanpa fakta',
                    'Informasi yang tidak dapat diverifikasi',
                ],
                [
                    'Sebelum menghubungi media untuk suatu isu, praktisi PR sebaiknya?',
                    'Memahami media, audiensnya, dan relevansi informasi yang akan disampaikan',
                    'Mengirim pesan sama ke semua pihak tanpa konteks',
                    'Mengabaikan kebutuhan informasi wartawan',
                    'Memberi data yang belum diverifikasi',
                ],
                [
                    'Organisasi memiliki berita yang relevan untuk industri teknologi. Strategi media relations yang paling tepat adalah?',
                    'Mengirim pitch yang relevan kepada jurnalis atau media yang memang meliput topik tersebut',
                    'Mengirim pesan yang sama ke seluruh kontak tanpa melihat bidang liputannya',
                    'Menghindari seluruh komunikasi dengan media',
                    'Menghapus informasi utama dari materi publikasi',
                ],
            ],
            'ikom-corporate-communication' => [
                [
                    'Sebelum menyusun pesan perusahaan untuk perubahan besar, langkah penting adalah?',
                    'Mengidentifikasi stakeholder dan kebutuhan informasi mereka',
                    'Memilih warna poster terlebih dahulu',
                    'Menghapus komunikasi internal',
                    'Menggunakan pesan sama tanpa mempertimbangkan audiens',
                ],
                [
                    'Corporate communication yang konsisten membantu organisasi dalam?',
                    'Menjaga keselarasan pesan dan reputasi di antara stakeholder',
                    'Menghapus seluruh kritik publik',
                    'Menggantikan strategi bisnis',
                    'Menghilangkan kebutuhan komunikasi internal',
                ],
                [
                    'Karyawan mengetahui perubahan organisasi dari media sebelum perusahaan memberi informasi internal. Masalah utama yang terjadi adalah?',
                    'Koordinasi komunikasi internal yang lemah',
                    'Database terlalu besar',
                    'Kualitas kamera buruk',
                    'Jaringan menggunakan VLAN',
                ],
            ],
            'ikom-crisis-communication' => [
                [
                    'Pesan awal singkat ketika krisis masih diselidiki sering disebut?',
                    'Holding statement',
                    'Balance sheet',
                    'Database migration',
                    'Source code patch',
                ],
                [
                    'Pada awal krisis, komunikasi organisasi sebaiknya mengutamakan?',
                    'Kecepatan, akurasi, empati, dan informasi yang telah diverifikasi',
                    'Spekulasi agar terlihat cepat',
                    'Diam tanpa penilaian kondisi',
                    'Menyalahkan pihak lain sebelum investigasi',
                ],
                [
                    'Menunjuk spokesperson pada situasi krisis membantu organisasi untuk?',
                    'Menjaga konsistensi dan koordinasi pesan kepada publik',
                    'Menghilangkan seluruh risiko krisis',
                    'Menggantikan investigasi',
                    'Mencegah media mengajukan pertanyaan',
                ],
            ],
            'ikom-news-writing' => [
                [
                    'Unsur dasar 5W+1H dalam berita mencakup?',
                    'What, who, when, where, why, dan how',
                    'Width, weight, web, window, write, dan host',
                    'Work, wage, wire, wall, word, dan home',
                    'Who saja',
                ],
                [
                    'Struktur inverted pyramid dalam penulisan berita menempatkan?',
                    'Informasi paling penting pada bagian awal',
                    'Informasi paling penting hanya di akhir',
                    'Opini reporter pada paragraf pertama',
                    'Iklan sebelum fakta utama',
                ],
                [
                    'Lead berita yang baik umumnya bertujuan untuk?',
                    'Menyampaikan informasi terpenting secara ringkas dan menarik',
                    'Menunda seluruh fakta sampai akhir',
                    'Memasukkan seluruh detail dalam satu kalimat panjang',
                    'Menggantikan seluruh isi berita',
                ],
                [
                    'Dalam penulisan berita dengan struktur inverted pyramid, informasi yang paling penting ditempatkan?',
                    'Pada bagian awal berita',
                    'Hanya pada paragraf terakhir',
                    'Di luar naskah berita',
                    'Secara acak tanpa prioritas informasi',
                ],
            ],
            'ikom-journalistic-interview' => [
                [
                    'Setelah narasumber memberi jawaban umum, teknik yang tepat untuk memperoleh detail adalah?',
                    'Mengajukan follow-up question yang relevan',
                    'Mengakhiri wawancara langsung',
                    'Mengubah jawaban narasumber sendiri',
                    'Mengabaikan informasi yang belum jelas',
                ],
                [
                    'Pertanyaan terbuka dalam wawancara jurnalistik berguna untuk?',
                    'Mendorong narasumber memberikan penjelasan yang lebih luas',
                    'Membatasi semua jawaban menjadi ya atau tidak',
                    'Memaksa narasumber menyetujui reporter',
                    'Menghilangkan kebutuhan follow-up',
                ],
                [
                    'Sebelum wawancara jurnalistik dilakukan, reporter sebaiknya?',
                    'Melakukan riset topik dan mempersiapkan pertanyaan utama',
                    'Datang tanpa memahami topik',
                    'Menentukan kesimpulan sebelum wawancara',
                    'Mengabaikan latar belakang narasumber',
                ],
            ],
            'ikom-news-reporting' => [
                [
                    'Menggunakan beberapa sumber independen untuk memeriksa informasi yang sama membantu meningkatkan?',
                    'Verifikasi dan kredibilitas laporan',
                    'Jumlah iklan',
                    'Ukuran file video',
                    'Kecepatan komputer',
                ],
                [
                    'Cara paling kuat untuk memverifikasi klaim statistik adalah?',
                    'Menelusuri data atau dokumen sumber primer yang dapat diperiksa',
                    'Mengandalkan jumlah likes',
                    'Mengutip unggahan tanpa sumber',
                    'Menganggap klaim benar karena sering dibagikan',
                ],
                [
                    'Dalam news reporting, reporter perlu membedakan dengan jelas antara?',
                    'Fakta yang terverifikasi dan opini',
                    'Warna dan ukuran font saja',
                    'File lokal dan cloud saja',
                    'Hardware dan software saja',
                ],
            ],
            'ikom-content-creation' => [
                [
                    'Konten digital bertujuan mendorong pengguna mendaftar ke sebuah acara. Elemen penting yang perlu dicantumkan adalah?',
                    'Call to action yang jelas',
                    'Informasi yang sengaja ambigu',
                    'Judul tanpa isi',
                    'Hashtag acak sebanyak mungkin',
                ],
                [
                    'Sebelum membuat konten, creator sebaiknya menentukan?',
                    'Tujuan komunikasi dan audiens yang ingin dijangkau',
                    'Jumlah efek visual sebanyak mungkin',
                    'Format secara acak',
                    'Caption sebelum memahami tujuan',
                ],
                [
                    'Storytelling dalam content creation terutama membantu?',
                    'Menyusun pesan agar lebih mudah dipahami dan memiliki alur yang menarik',
                    'Menggantikan seluruh fakta',
                    'Menghilangkan kebutuhan memahami audiens',
                    'Menjamin seluruh konten viral',
                ],
                [
                    'Konten digital dibuat untuk mendorong pengguna mendaftar ke sebuah acara. Elemen yang paling penting agar tujuan tindakan terlihat jelas adalah?',
                    'Call to action yang sesuai dengan tujuan pendaftaran',
                    'Menambah dekorasi tanpa hubungan dengan tujuan',
                    'Menghilangkan informasi cara mendaftar',
                    'Menggunakan sebanyak mungkin pesan yang saling berbeda',
                ],
            ],
            'ikom-social-media-management' => [
                [
                    'Content calendar berguna terutama untuk?',
                    'Merencanakan waktu, topik, format, dan kanal publikasi secara konsisten',
                    'Menggantikan seluruh analisis audiens',
                    'Menentukan password akun',
                    'Menghapus seluruh metrik performa',
                ],
                [
                    'Engagement rate pada media sosial membantu mengukur?',
                    'Interaksi audiens terhadap konten relatif terhadap basis pengukuran yang digunakan',
                    'Jumlah kabel jaringan',
                    'Nilai aset perusahaan',
                    'Kecepatan processor',
                ],
                [
                    'Ketika komentar negatif mulai meningkat, social media manager sebaiknya?',
                    'Memantau konteks, merespons sesuai pedoman, dan mengeskalasi isu serius',
                    'Menghapus seluruh komentar tanpa penilaian',
                    'Membalas secara emosional',
                    'Mengabaikan semua komentar',
                ],
            ],
            'ikom-video-production' => [
                [
                    'Tahap sebelum pengambilan gambar yang mencakup konsep, script, dan shot list disebut?',
                    'Pre-production',
                    'Post-production',
                    'Distribution',
                    'Archiving',
                ],
                [
                    'Shot list terutama digunakan untuk?',
                    'Merencanakan gambar atau shot yang perlu direkam saat produksi',
                    'Mengatur database',
                    'Menghitung rasio keuangan',
                    'Membuat konfigurasi jaringan',
                ],
                [
                    'Dalam produksi video, audio dialog yang jelas penting karena?',
                    'Membantu audiens memahami pesan utama video',
                    'Audio tidak memengaruhi pengalaman penonton',
                    'Audio hanya dibutuhkan untuk video tanpa gambar',
                    'Kualitas audio selalu dapat diabaikan',
                ],
            ],
        ];
    }
}
