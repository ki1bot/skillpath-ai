<?php

namespace Database\Seeders\AssessmentQuestions;

final class ManajemenQuestionBank implements AssessmentQuestionBank
{
    public static function studyProgram(): string
    {
        return 'Manajemen';
    }

    public static function questions(): array
    {
        return [
            'man-branding' => [
                [
                    'Sebuah merek ingin dikenal sebagai produk premium yang sederhana dan tahan lama. Hal ini terutama berkaitan dengan?',
                    'Brand positioning',
                    'Payroll',
                    'Inventory turnover',
                    'Recruitment funnel',
                ],
                [
                    'Perusahaan menggunakan pesan, warna, dan gaya komunikasi yang konsisten di seluruh kanal. Tujuan utamanya adalah?',
                    'Memperkuat identitas dan pengenalan merek',
                    'Mengurangi jumlah karyawan',
                    'Mengubah struktur modal',
                    'Menghapus market research',
                ],
                [
                    'Dua produk memiliki fitur hampir sama, tetapi perusahaan ingin mereknya dipersepsikan berbeda dari pesaing. Konsep yang paling relevan adalah?',
                    'Brand differentiation',
                    'Payroll processing',
                    'Debt restructuring',
                    'Candidate scoring',
                ],
                [
                    'Sebuah perusahaan ingin logo, tipografi, warna, dan gaya komunikasinya digunakan secara konsisten oleh semua tim. Dokumen yang paling tepat digunakan adalah?',
                    'Brand guideline',
                    'Laporan arus kas',
                    'Daftar inventaris',
                    'Job description',
                ],
            ],
            'man-digital-marketing' => [
                [
                    'Kampanye iklan digital bertujuan menghasilkan pendaftaran. Metrik paling langsung untuk mengevaluasi tujuan tersebut adalah?',
                    'Conversion rate pendaftaran',
                    'Ukuran logo',
                    'Jumlah warna banner',
                    'Jumlah halaman dokumen internal',
                ],
                [
                    'Iklan mendapat 10.000 impresi dan 500 klik. Metrik yang membandingkan klik terhadap impresi adalah?',
                    'Click-through rate',
                    'Current ratio',
                    'Employee turnover',
                    'Inventory days',
                ],
                [
                    'Dua kampanye menghasilkan jumlah pelanggan baru sama, tetapi kampanye A menggunakan biaya lebih kecil. Metrik yang tepat untuk membandingkan efisiensi adalah?',
                    'Cost per acquisition',
                    'Jumlah posting',
                    'Jumlah karyawan',
                    'Current asset ratio',
                ],
            ],
            'man-market-research' => [
                [
                    'Perusahaan ingin mengetahui alasan pelanggan berpindah ke kompetitor. Data paling relevan dikumpulkan melalui?',
                    'Wawancara atau survei pelanggan yang berpindah',
                    'Daftar warna kantor',
                    'Jumlah perangkat karyawan',
                    'Nama file laporan lama',
                ],
                [
                    'Sampel penelitian hanya berasal dari pelanggan paling loyal. Risiko utama metode tersebut adalah?',
                    'Sampling bias',
                    'Brand awareness terlalu tinggi',
                    'Likuiditas perusahaan turun',
                    'Semua data otomatis menjadi kualitatif',
                ],
                [
                    'Data yang dikumpulkan perusahaan sendiri melalui survei pelanggan termasuk?',
                    'Primary data',
                    'Data yang tidak dapat dianalisis',
                    'Data jaringan komputer',
                    'Data akuntansi wajib',
                ],
            ],
            'man-financial-planning' => [
                [
                    'Dalam financial planning, proyeksi arus kas terutama digunakan untuk?',
                    'Memperkirakan kemampuan memenuhi kebutuhan kas pada periode mendatang',
                    'Menentukan warna merek',
                    'Menyusun struktur organisasi',
                    'Menilai kualitas wawancara',
                ],
                [
                    'Perusahaan memperkirakan pemasukan dan pengeluaran untuk dua belas bulan berikutnya. Aktivitas tersebut merupakan bagian dari?',
                    'Budgeting dan financial planning',
                    'Brand positioning',
                    'Job analysis',
                    'Market segmentation',
                ],
                [
                    'Perusahaan membuat skenario optimistis, normal, dan pesimistis untuk proyeksi keuangan. Tujuan utamanya adalah?',
                    'Memahami dampak berbagai kondisi terhadap rencana keuangan',
                    'Menjamin satu skenario pasti terjadi',
                    'Menghapus seluruh risiko',
                    'Menggantikan pencatatan transaksi',
                ],
                [
                    'Pendapatan aktual perusahaan lebih rendah dari proyeksi sementara pengeluaran tetap sama. Langkah financial planning yang paling tepat adalah?',
                    'Memperbarui proyeksi arus kas dan menyesuaikan rencana pengeluaran',
                    'Mengabaikan perubahan karena anggaran sudah dibuat',
                    'Menghapus seluruh catatan transaksi',
                    'Menambah pengeluaran tanpa menghitung kemampuan kas',
                ],
            ],
            'man-financial-analysis' => [
                [
                    'Analisis tren laporan keuangan dilakukan terutama untuk?',
                    'Melihat perubahan kinerja dan posisi keuangan dari waktu ke waktu',
                    'Menentukan slogan pemasaran',
                    'Menilai desain antarmuka',
                    'Mengatur topologi jaringan',
                ],
                [
                    'Pendapatan naik tetapi laba bersih turun. Analisis berikutnya yang paling relevan adalah?',
                    'Memeriksa perubahan biaya dan margin keuntungan',
                    'Mengganti logo perusahaan',
                    'Mengubah seluruh deskripsi pekerjaan',
                    'Mengabaikan laporan laba rugi',
                ],
                [
                    'Perusahaan memiliki laba positif tetapi arus kas operasi terus negatif. Hal yang paling tepat dilakukan adalah?',
                    'Menganalisis kualitas laba dan pergerakan kas perusahaan',
                    'Menganggap kondisi pasti sehat karena laba positif',
                    'Mengabaikan laporan arus kas',
                    'Mengganti strategi branding',
                ],
            ],
            'man-investment-management' => [
                [
                    'Prinsip dasar hubungan risiko dan imbal hasil dalam investasi adalah?',
                    'Potensi imbal hasil lebih tinggi umumnya disertai risiko lebih tinggi',
                    'Semua investasi memberikan hasil pasti',
                    'Risiko tidak perlu dipertimbangkan',
                    'Diversifikasi selalu menghapus seluruh risiko',
                ],
                [
                    'Investor membagi dana ke beberapa jenis aset dengan karakteristik berbeda. Tujuan utama tindakan tersebut adalah?',
                    'Diversifikasi risiko',
                    'Menghilangkan seluruh risiko investasi',
                    'Menjamin keuntungan tetap',
                    'Menghindari analisis investasi',
                ],
                [
                    'Dua investasi memiliki return yang diperkirakan sama tetapi salah satunya memiliki risiko jauh lebih rendah. Secara umum pilihan yang lebih efisien adalah?',
                    'Investasi dengan risiko lebih rendah setelah asumsi diverifikasi',
                    'Investasi dengan risiko lebih tinggi tanpa alasan',
                    'Memilih secara acak',
                    'Mengabaikan risiko',
                ],
            ],
            'man-recruitment-selection' => [
                [
                    'Sebelum membuka lowongan, langkah rekrutmen yang paling tepat adalah?',
                    'Menetapkan kebutuhan jabatan dan profil kandidat',
                    'Mengiklankan posisi tanpa deskripsi kerja',
                    'Memilih kandidat pertama',
                    'Mengabaikan kebutuhan organisasi',
                ],
                [
                    'Sebelum menentukan kualifikasi kandidat, perusahaan perlu memahami tugas dan tanggung jawab posisi melalui?',
                    'Job analysis',
                    'Brand audit',
                    'Financial forecasting',
                    'Market segmentation',
                ],
                [
                    'Agar proses seleksi kandidat konsisten dan adil, perusahaan sebaiknya?',
                    'Menggunakan kriteria kompetensi dan metode penilaian yang terstruktur',
                    'Mengandalkan intuisi pewawancara saja',
                    'Memilih berdasarkan foto profil',
                    'Mengubah kriteria untuk setiap kandidat',
                ],
                [
                    'Perusahaan ingin membandingkan kandidat secara lebih konsisten dan mengurangi penilaian berdasarkan kesan pribadi pewawancara. Pendekatan yang paling tepat adalah?',
                    'Menggunakan wawancara terstruktur dengan pertanyaan dan rubrik yang sama',
                    'Memberikan pertanyaan berbeda tanpa kriteria penilaian',
                    'Memilih kandidat hanya berdasarkan intuisi pewawancara',
                    'Mengabaikan kompetensi yang dibutuhkan posisi',
                ],
            ],
            'man-performance-management' => [
                [
                    'Sasaran kinerja yang baik seharusnya?',
                    'Spesifik, terukur, relevan, dan memiliki batas waktu',
                    'Berubah setiap hari tanpa alasan',
                    'Tidak memiliki indikator',
                    'Hanya diketahui atasan',
                ],
                [
                    'Umpan balik kinerja yang efektif sebaiknya?',
                    'Spesifik, berdasarkan perilaku atau hasil, dan memberikan arah perbaikan',
                    'Hanya diberikan ketika terjadi kesalahan besar',
                    'Berdasarkan rumor',
                    'Tidak dikaitkan dengan sasaran kerja',
                ],
                [
                    'KPI dalam performance management terutama digunakan untuk?',
                    'Mengukur pencapaian terhadap sasaran kinerja yang ditetapkan',
                    'Menggantikan seluruh komunikasi antara atasan dan karyawan',
                    'Menentukan warna identitas perusahaan',
                    'Menghapus kebutuhan evaluasi',
                ],
            ],
            'man-talent-management' => [
                [
                    'Succession planning bertujuan untuk?',
                    'Menyiapkan talenta bagi peran penting di masa depan',
                    'Menghapus semua program pengembangan',
                    'Mengganti seluruh karyawan setiap tahun',
                    'Menghindari evaluasi kompetensi',
                ],
                [
                    'Program pengembangan karyawan berpotensi tinggi terutama bertujuan untuk?',
                    'Mempersiapkan kemampuan mereka bagi tanggung jawab yang lebih besar',
                    'Menghapus seluruh proses evaluasi',
                    'Menghindari succession planning',
                    'Mengurangi seluruh pelatihan',
                ],
                [
                    'Perusahaan kehilangan banyak karyawan berkinerja tinggi. Analisis talent management yang paling relevan adalah?',
                    'Menganalisis faktor retensi, pengembangan, penghargaan, dan peluang karier',
                    'Mengabaikan alasan karyawan keluar',
                    'Menghapus program pengembangan',
                    'Mengganti seluruh proses rekrutmen tanpa analisis',
                ],
            ],
        ];
    }
}
