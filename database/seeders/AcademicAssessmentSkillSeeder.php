<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class AcademicAssessmentSkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            ['si-sql-data-processing', 'SQL dan Pengolahan Data', 'Analisis Data', 'Menggunakan SQL untuk mengambil, menggabungkan, membersihkan, dan mengolah data secara terstruktur.', 'Menengah'],
            ['si-spreadsheet-data-analysis', 'Spreadsheet dan Analisis Data', 'Analisis Data', 'Mengolah dan menganalisis data menggunakan formula, fungsi, tabel pivot, filter, dan validasi pada spreadsheet.', 'Dasar'],
            ['si-business-intelligence-data-visualization', 'Business Intelligence dan Visualisasi Data', 'Analisis Data', 'Menyusun dashboard, KPI, dan visualisasi data yang mendukung pengambilan keputusan.', 'Menengah'],
            ['si-database-management', 'Database Management', 'Pengembangan Sistem', 'Merancang, mengelola, menjaga integritas, dan mengoptimalkan basis data untuk kebutuhan sistem informasi.', 'Menengah'],
            ['si-web-development', 'Web Development', 'Pengembangan Sistem', 'Membangun aplikasi web dengan memahami frontend, backend, integrasi API, dan alur data.', 'Menengah'],
            ['si-system-analysis-design', 'System Analysis and Design', 'Pengembangan Sistem', 'Menganalisis kebutuhan serta merancang proses, data, dan arsitektur sistem yang sesuai.', 'Menengah'],
            ['si-ui-design', 'UI Design', 'UI/UX', 'Menyusun antarmuka yang konsisten, mudah dipahami, dan sesuai prinsip visual.', 'Dasar'],
            ['si-wireframing-prototyping', 'Wireframing dan Prototyping', 'UI/UX', 'Membuat wireframe dan prototipe untuk memvalidasi struktur serta alur interaksi sebelum implementasi.', 'Dasar'],
            ['si-user-research', 'User Research', 'UI/UX', 'Mengumpulkan dan menganalisis kebutuhan pengguna melalui riset, wawancara, observasi, atau pengujian.', 'Menengah'],
            ['man-branding', 'Branding', 'Marketing', 'Mengelola identitas, positioning, proposisi nilai, dan persepsi merek pada target pasar.', 'Menengah'],
            ['man-digital-marketing', 'Digital Marketing', 'Marketing', 'Merencanakan dan mengevaluasi pemasaran melalui kanal digital berdasarkan tujuan, audiens, dan metrik.', 'Menengah'],
            ['man-market-research', 'Market Research', 'Marketing', 'Mengumpulkan dan menganalisis data pasar untuk memahami pelanggan, pesaing, dan peluang.', 'Menengah'],
            ['man-financial-planning', 'Financial Planning', 'Keuangan', 'Menyusun rencana keuangan berdasarkan tujuan, arus kas, kebutuhan dana, dan risiko.', 'Menengah'],
            ['man-financial-analysis', 'Financial Analysis', 'Keuangan', 'Menganalisis laporan dan indikator keuangan untuk menilai kondisi serta kinerja organisasi.', 'Menengah'],
            ['man-investment-management', 'Investment Management', 'Keuangan', 'Menilai instrumen investasi, hubungan risiko dan imbal hasil, serta komposisi portofolio.', 'Menengah'],
            ['man-recruitment-selection', 'Recruitment and Selection', 'Human Resources', 'Merancang proses rekrutmen dan seleksi yang relevan dengan kebutuhan jabatan dan organisasi.', 'Menengah'],
            ['man-performance-management', 'Performance Management', 'Human Resources', 'Menetapkan sasaran, indikator kinerja, umpan balik, dan evaluasi kinerja secara sistematis.', 'Menengah'],
            ['man-talent-management', 'Talent Management', 'Human Resources', 'Mengidentifikasi, mengembangkan, dan mempertahankan talenta untuk kebutuhan organisasi jangka panjang.', 'Menengah'],
            ['ti-algorithms-data-structures', 'Algoritma dan Struktur Data', 'Pemrograman dan Rekayasa Perangkat Lunak', 'Memilih algoritma dan struktur data yang sesuai berdasarkan karakteristik masalah dan efisiensi.', 'Menengah'],
            ['ti-object-oriented-programming', 'Object-Oriented Programming', 'Pemrograman dan Rekayasa Perangkat Lunak', 'Menerapkan enkapsulasi, abstraksi, pewarisan, dan polimorfisme dalam desain perangkat lunak.', 'Menengah'],
            ['ti-software-engineering', 'Software Engineering', 'Pemrograman dan Rekayasa Perangkat Lunak', 'Menerapkan proses rekayasa perangkat lunak mulai dari kebutuhan, desain, implementasi, pengujian, hingga pemeliharaan.', 'Menengah'],
            ['ti-computer-networks', 'Computer Networks', 'Jaringan dan Sistem Komputer', 'Memahami komunikasi jaringan, addressing, routing, protokol, dan troubleshooting dasar.', 'Menengah'],
            ['ti-operating-systems', 'Operating Systems', 'Jaringan dan Sistem Komputer', 'Memahami proses, thread, memori, sistem berkas, sinkronisasi, dan pengelolaan resource sistem operasi.', 'Menengah'],
            ['ti-cybersecurity', 'Cybersecurity', 'Jaringan dan Sistem Komputer', 'Menerapkan prinsip keamanan, kontrol akses, perlindungan data, dan mitigasi ancaman dasar.', 'Menengah'],
            ['ti-machine-learning', 'Machine Learning', 'Artificial Intelligence', 'Memahami proses pelatihan, evaluasi, generalisasi, dan pemilihan model machine learning.', 'Menengah'],
            ['ti-data-science', 'Data Science', 'Artificial Intelligence', 'Menggabungkan pengolahan data, statistik, pemrograman, dan interpretasi untuk memperoleh insight.', 'Menengah'],
            ['ti-computer-vision', 'Computer Vision', 'Artificial Intelligence', 'Memahami representasi citra dan pendekatan komputasional untuk klasifikasi, deteksi, atau analisis visual.', 'Menengah'],
            ['sk-computer-architecture', 'Computer Architecture', 'Arsitektur dan Organisasi Komputer', 'Memahami struktur CPU, memori, bus, instruction cycle, dan hubungan antar komponen utama pada sistem komputer.', 'Menengah'],
            ['sk-digital-logic', 'Digital Logic', 'Arsitektur dan Organisasi Komputer', 'Memahami gerbang logika, aljabar Boolean, rangkaian kombinasi, dan rangkaian sekuensial sebagai dasar sistem digital.', 'Dasar'],
            ['sk-microprocessor-microcontroller', 'Microprocessor and Microcontroller', 'Arsitektur dan Organisasi Komputer', 'Memahami cara kerja mikroprosesor dan mikrokontroler, register, interrupt, memory map, dan antarmuka I/O.', 'Menengah'],
            ['sk-embedded-systems', 'Embedded Systems', 'Embedded System dan Internet of Things', 'Merancang sistem tertanam yang menggabungkan perangkat keras, firmware, input-output, dan batasan resource untuk fungsi tertentu.', 'Menengah'],
            ['sk-internet-of-things', 'Internet of Things', 'Embedded System dan Internet of Things', 'Memahami alur perangkat IoT dari sensor, konektivitas, protokol komunikasi, pengiriman data, hingga layanan aplikasi.', 'Menengah'],
            ['sk-sensor-actuator-integration', 'Sensor and Actuator Integration', 'Embedded System dan Internet of Things', 'Mengintegrasikan sensor dan aktuator dengan mikrokontroler melalui antarmuka, pembacaan data, kalibrasi, dan kontrol output.', 'Menengah'],
            ['sk-computer-networks', 'Computer Networks', 'Jaringan dan Keamanan Komputer', 'Memahami addressing, switching, routing, protokol, subnetting, dan komunikasi antar perangkat pada jaringan komputer.', 'Menengah'],
            ['sk-network-administration', 'Network Administration', 'Jaringan dan Keamanan Komputer', 'Mengelola konfigurasi jaringan, layanan dasar, perangkat, pengguna, monitoring, dan troubleshooting operasional.', 'Menengah'],
            ['sk-network-security', 'Network Security', 'Jaringan dan Keamanan Komputer', 'Menerapkan prinsip keamanan jaringan melalui segmentasi, firewall, kontrol akses, monitoring, dan mitigasi ancaman.', 'Menengah'],
            ['psi-employee-behavior', 'Employee Behavior', 'Psikologi Industri dan Organisasi', 'Memahami faktor psikologis yang memengaruhi perilaku, motivasi, dan kinerja individu di tempat kerja.', 'Menengah'],
            ['psi-organizational-development', 'Organizational Development', 'Psikologi Industri dan Organisasi', 'Menganalisis perubahan organisasi dan merancang intervensi untuk meningkatkan efektivitas organisasi.', 'Menengah'],
            ['psi-psychological-assessment', 'Psychological Assessment', 'Psikologi Industri dan Organisasi', 'Memahami prinsip dasar penggunaan alat Assesment psikologis secara tepat, etis, dan berbasis tujuan.', 'Menengah'],
            ['psi-counseling-skills', 'Counseling Skills', 'Konseling', 'Menerapkan keterampilan dasar konseling seperti attending, active listening, probing, dan refleksi.', 'Menengah'],
            ['psi-interpersonal-communication', 'Interpersonal Communication', 'Konseling', 'Membangun komunikasi dua arah yang jelas, empatik, dan sesuai konteks hubungan interpersonal.', 'Dasar'],
            ['psi-emotional-intelligence', 'Emotional Intelligence', 'Konseling', 'Mengenali, memahami, dan mengelola emosi diri serta merespons emosi orang lain secara adaptif.', 'Menengah'],
            ['psi-research-methodology', 'Research Methodology', 'Penelitian Psikologi', 'Memilih desain, variabel, sampel, instrumen, dan prosedur penelitian sesuai pertanyaan penelitian.', 'Menengah'],
            ['psi-interview-observation', 'Interview dan Observation', 'Penelitian Psikologi', 'Mengumpulkan data perilaku dan pengalaman secara sistematis melalui wawancara dan observasi.', 'Menengah'],
            ['psi-survey-data-analysis', 'Survey dan Data Analysis', 'Penelitian Psikologi', 'Merancang survei dan menganalisis data untuk menjawab pertanyaan penelitian secara terukur.', 'Menengah'],
            ['ikom-media-relations', 'Media Relations', 'Public Relations', 'Membangun hubungan profesional dengan media dan mengelola informasi organisasi untuk kebutuhan pemberitaan.', 'Menengah'],
            ['ikom-corporate-communication', 'Corporate Communication', 'Public Relations', 'Mengelola pesan dan komunikasi organisasi kepada pemangku kepentingan internal maupun eksternal.', 'Menengah'],
            ['ikom-crisis-communication', 'Crisis Communication', 'Public Relations', 'Merencanakan komunikasi pada situasi krisis untuk menjaga kejelasan informasi, kepercayaan, dan reputasi.', 'Menengah'],
            ['ikom-news-writing', 'News Writing', 'Jurnalistik', 'Menulis berita secara faktual, terstruktur, ringkas, dan sesuai nilai berita.', 'Dasar'],
            ['ikom-journalistic-interview', 'Journalistic Interview', 'Jurnalistik', 'Merancang dan melakukan wawancara jurnalistik untuk memperoleh informasi yang relevan dan dapat diverifikasi.', 'Menengah'],
            ['ikom-news-reporting', 'News Reporting', 'Jurnalistik', 'Mengumpulkan, memverifikasi, dan menyusun fakta lapangan menjadi laporan berita yang akurat.', 'Menengah'],
            ['ikom-content-creation', 'Content Creation', 'Digital Media', 'Merancang konten berdasarkan tujuan, audiens, format, pesan, dan karakter kanal digital.', 'Menengah'],
            ['ikom-social-media-management', 'Social Media Management', 'Digital Media', 'Merencanakan publikasi, interaksi, analitik, dan evaluasi performa akun media sosial.', 'Menengah'],
            ['ikom-video-production', 'Video Production', 'Digital Media', 'Merencanakan dan memproduksi video melalui tahap pra-produksi, produksi, dan pascaproduksi.', 'Menengah'],
        ];

        foreach ($skills as [$slug, $name, $category, $description, $difficulty]) {
            Skill::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'category' => $category,
                    'description' => $description,
                    'difficulty' => $difficulty,
                ],
            );
        }
    }
}
