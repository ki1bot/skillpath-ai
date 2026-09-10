<?php

namespace Database\Seeders\AssessmentQuestions;

final class PsikologiQuestionBank implements AssessmentQuestionBank
{
    public static function studyProgram(): string
    {
        return 'Psikologi';
    }

    public static function questions(): array
    {
        return [
            'psi-employee-behavior' => [
                [
                    'Karyawan menunjukkan penurunan kepuasan setelah beban kerja meningkat tanpa dukungan tambahan. Faktor yang paling relevan dianalisis adalah?',
                    'Kondisi kerja, beban kerja, dan dukungan organisasi',
                    'Jenis database',
                    'Topologi router',
                    'Ukuran monitor',
                ],
                [
                    'Nilai, norma, dan kebiasaan bersama yang membentuk cara anggota organisasi bertindak disebut?',
                    'Organizational culture',
                    'Network protocol',
                    'Financial ratio',
                    'Database schema',
                ],
                [
                    'Seorang karyawan memiliki kemampuan baik tetapi motivasinya turun setelah merasa kontribusinya tidak dihargai. Faktor yang paling relevan dianalisis adalah?',
                    'Motivasi dan persepsi penghargaan dalam pekerjaan',
                    'Kecepatan jaringan',
                    'Struktur tabel database',
                    'Resolusi layar',
                ],
                [
                    'Perusahaan menemukan peningkatan ketidakhadiran dan keinginan karyawan untuk keluar. Data tambahan yang paling relevan untuk memahami perilaku tersebut adalah?',
                    'Kepuasan kerja dan faktor lingkungan kerja karyawan',
                    'Warna logo perusahaan',
                    'Jenis sistem operasi komputer kantor',
                    'Resolusi monitor yang digunakan',
                ],
            ],
            'psi-organizational-development' => [
                [
                    'Perubahan organisasi sering ditolak oleh karyawan. Langkah yang membantu proses perubahan adalah?',
                    'Melibatkan stakeholder dan menjelaskan alasan serta dampak perubahan',
                    'Menyembunyikan seluruh informasi',
                    'Mengabaikan kekhawatiran karyawan',
                    'Mengubah struktur setiap hari',
                ],
                [
                    'Sebelum menentukan intervensi organizational development, organisasi sebaiknya?',
                    'Melakukan diagnosis terhadap masalah dan kebutuhan organisasi',
                    'Langsung menerapkan perubahan tanpa data',
                    'Menghapus seluruh kebijakan',
                    'Menentukan hasil tanpa evaluasi',
                ],
                [
                    'Setelah intervensi pengembangan organisasi dilakukan, langkah penting berikutnya adalah?',
                    'Mengevaluasi dampak intervensi menggunakan indikator yang relevan',
                    'Menganggap intervensi selalu berhasil',
                    'Menghentikan seluruh pengumpulan data',
                    'Mengabaikan feedback anggota organisasi',
                ],
            ],
            'psi-psychological-assessment' => [
                [
                    'Reliabilitas instrumen psikologi merujuk pada?',
                    'Konsistensi hasil pengukuran',
                    'Keindahan tampilan instrumen',
                    'Jumlah halaman instrumen',
                    'Popularitas instrumen',
                ],
                [
                    'Validitas instrumen psikologi berkaitan dengan?',
                    'Sejauh mana instrumen mengukur konstruk yang seharusnya diukur',
                    'Jumlah warna pada formulir',
                    'Ukuran file data',
                    'Kecepatan komputer',
                ],
                [
                    'Hasil psychological assessment paling tepat digunakan dengan cara?',
                    'Diinterpretasikan sesuai tujuan, prosedur, konteks, dan batasan instrumen',
                    'Digunakan sebagai satu-satunya dasar keputusan tanpa konteks',
                    'Dianggap selalu sempurna',
                    'Dibagikan kepada siapa pun tanpa memperhatikan kerahasiaan',
                ],
            ],
            'psi-counseling-skills' => [
                [
                    'Pertanyaan terbuka dalam konseling bermanfaat karena?',
                    'Memberikan ruang kepada klien menjelaskan pengalaman dengan lebih luas',
                    'Membatasi jawaban hanya ya atau tidak',
                    'Memaksa klien menerima nasihat',
                    'Menghilangkan kebutuhan mendengarkan',
                ],
                [
                    'Active listening dalam konseling ditunjukkan dengan?',
                    'Memperhatikan, mengklarifikasi, dan merespons isi pembicaraan secara tepat',
                    'Memotong pembicaraan terus-menerus',
                    'Mengubah topik setiap saat',
                    'Membuat asumsi tanpa klarifikasi',
                ],
                [
                    'Teknik merangkum dalam konseling digunakan untuk?',
                    'Menyatukan poin penting pembicaraan dan memastikan pemahaman',
                    'Mengakhiri pembicaraan tanpa alasan',
                    'Mengubah cerita klien',
                    'Memberikan diagnosis otomatis',
                ],
                [
                    'Dalam sesi konseling, konselor mengulangi inti pernyataan klien dengan kalimat sendiri untuk memastikan pemahaman. Teknik tersebut merupakan contoh?',
                    'Paraphrasing dalam active listening',
                    'Memberikan diagnosis tanpa penggalian informasi',
                    'Mengalihkan pembicaraan ke pengalaman konselor',
                    'Menghentikan klien sebelum selesai berbicara',
                ],
            ],
            'psi-interpersonal-communication' => [
                [
                    'Active listening dalam komunikasi interpersonal ditunjukkan dengan?',
                    'Memperhatikan, mengklarifikasi, dan memberikan respons yang sesuai',
                    'Memotong pembicaraan',
                    'Mengalihkan topik',
                    'Membuat asumsi tanpa klarifikasi',
                ],
                [
                    'Komunikasi asertif berarti?',
                    'Menyampaikan kebutuhan dan pendapat dengan jelas sambil menghormati orang lain',
                    'Memaksakan kehendak kepada orang lain',
                    'Menghindari seluruh perbedaan pendapat',
                    'Tidak pernah menyampaikan kebutuhan',
                ],
                [
                    'Bahasa tubuh, ekspresi wajah, dan kontak mata termasuk?',
                    'Komunikasi nonverbal',
                    'Analisis statistik',
                    'Network communication',
                    'Database query',
                ],
            ],
            'psi-emotional-intelligence' => [
                [
                    'Kemampuan menahan respons impulsif ketika sedang marah merupakan bagian dari?',
                    'Self-regulation',
                    'Database management',
                    'Market research',
                    'Network administration',
                ],
                [
                    'Kemampuan memahami perasaan orang lain dari sudut pandangnya berkaitan dengan?',
                    'Empathy',
                    'Financial analysis',
                    'Digital logic',
                    'Database indexing',
                ],
                [
                    'Kemampuan mengenali emosi diri sendiri dan pengaruhnya terhadap perilaku disebut?',
                    'Self-awareness',
                    'Routing',
                    'Normalization',
                    'Brand positioning',
                ],
            ],
            'psi-research-methodology' => [
                [
                    'Definisi operasional variabel dibutuhkan agar?',
                    'Konsep penelitian dapat diukur atau diamati secara jelas',
                    'Hipotesis selalu terbukti',
                    'Semua responden memberi jawaban sama',
                    'Analisis statistik tidak diperlukan',
                ],
                [
                    'Hipotesis penelitian pada dasarnya merupakan?',
                    'Pernyataan sementara yang dapat diuji menggunakan data',
                    'Kesimpulan akhir yang tidak dapat diubah',
                    'Daftar responden',
                    'Instrumen penelitian',
                ],
                [
                    'Pemilihan sampel yang sesuai penting karena?',
                    'Mempengaruhi kualitas inferensi terhadap populasi yang diteliti',
                    'Menjamin seluruh hasil selalu benar',
                    'Menghapus kebutuhan metode penelitian',
                    'Menghilangkan seluruh bias secara otomatis',
                ],
                [
                    'Dalam eksperimen, peserta dibagi secara acak ke kelompok perlakuan dan kontrol. Tujuan utama random assignment adalah?',
                    'Mengurangi pengaruh perbedaan awal antar kelompok terhadap hasil penelitian',
                    'Menjamin semua peserta memberikan jawaban yang sama',
                    'Menghilangkan kebutuhan untuk menganalisis data',
                    'Mengubah penelitian kuantitatif menjadi kualitatif',
                ],
            ],
            'psi-interview-observation' => [
                [
                    'Pewawancara hanya mencari informasi yang mendukung dugaan awalnya. Hal tersebut berisiko menimbulkan?',
                    'Confirmation bias',
                    'Random sampling',
                    'Reliabilitas sempurna',
                    'Validitas otomatis',
                ],
                [
                    'Dua observer memberikan skor sangat berbeda untuk perilaku yang sama. Aspek yang perlu ditingkatkan adalah?',
                    'Inter-rater reliability',
                    'Ukuran font',
                    'Jumlah folder penelitian',
                    'Warna lembar observasi',
                ],
                [
                    'Catatan observasi yang baik sebaiknya?',
                    'Membedakan deskripsi perilaku yang diamati dari interpretasi observer',
                    'Hanya berisi opini observer',
                    'Menghilangkan konteks kejadian',
                    'Ditulis berdasarkan ingatan beberapa minggu kemudian saja',
                ],
            ],
            'psi-survey-data-analysis' => [
                [
                    'Skala Likert umumnya digunakan untuk mengukur?',
                    'Tingkat sikap atau persetujuan responden terhadap pernyataan',
                    'Alamat IP responden',
                    'Kecepatan CPU',
                    'Struktur database',
                ],
                [
                    'Analisis deskriptif digunakan terutama untuk?',
                    'Merangkum dan menggambarkan karakteristik data yang diperoleh',
                    'Membuktikan seluruh hubungan bersifat kausal',
                    'Menghilangkan kebutuhan pengumpulan data',
                    'Mengganti metode penelitian',
                ],
                [
                    'Sebelum menganalisis hasil survey, data sebaiknya diperiksa untuk?',
                    'Menemukan data kosong, nilai tidak valid, dan ketidakkonsistenan',
                    'Mengubah seluruh jawaban agar sama',
                    'Menghapus semua responden',
                    'Memastikan hipotesis pasti benar',
                ],
            ],
        ];
    }
}
