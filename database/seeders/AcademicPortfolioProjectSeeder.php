<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\PortfolioProject;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class AcademicPortfolioProjectSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            ['Sistem Informasi', 'Sales & Business Intelligence Dashboard', 'sales-business-intelligence-dashboard', 'Membangun dashboard Business Intelligence dari dataset penjualan perusahaan untuk menghasilkan insight dan rekomendasi bisnis.', 'Diberikan dataset perusahaan. Lakukan cleaning data, analisis, pembuatan dashboard, penarikan insight, dan penyusunan rekomendasi bisnis.', 16, ['Membersihkan dataset perusahaan', 'Melakukan analisis data penjualan', 'Membangun dashboard Business Intelligence', 'Menuliskan insight utama dari data', 'Menyusun rekomendasi bisnis berdasarkan hasil analisis'], ['si-sql-data-processing', 'si-spreadsheet-data-analysis', 'si-business-intelligence-data-visualization', 'si-data-visualization', 'si-scenario-based-data-analysis']],
            ['Sistem Informasi', 'Build Mini Information System', 'build-mini-information-system', 'Membangun sistem informasi mini dari sebuah studi kasus dengan database, UI, backend, dan fitur utama yang dapat digunakan.', 'Diberikan studi kasus. Rancang dan bangun sistem informasi lengkap dengan database, UI, backend, dan fitur utama.', 24, ['Menganalisis kebutuhan dari studi kasus', 'Membuat ERD/UML yang diperlukan', 'Membangun database', 'Membangun UI dan backend', 'Menyelesaikan fitur utama dan menguji alur sistem'], ['si-database-management', 'si-web-development', 'si-system-analysis-design', 'si-erd-uml', 'si-problem-solving']],
            ['Sistem Informasi', 'Redesign Digital Product', 'redesign-digital-product', 'Merancang ulang produk digital berdasarkan masalah pengguna melalui riset, persona, user flow, wireframe, prototype, dan usability testing.', 'Diberikan masalah pada sebuah produk digital. Lakukan research, buat persona dan user flow, lanjutkan ke wireframe, prototype, lalu usability testing.', 18, ['Melakukan user research', 'Membuat persona', 'Menyusun user flow', 'Membuat wireframe', 'Membuat prototype dan melakukan usability testing'], ['si-ui-design', 'si-wireframing-prototyping', 'si-prototyping', 'si-user-research', 'si-usability']],

            ['Manajemen', 'Digital Marketing Campaign', 'digital-marketing-campaign', 'Merancang kampanye digital dari target market hingga evaluasi KPI.', 'Tentukan target market, susun branding dan content strategy, jalankan rancangan campaign, tentukan KPI, lalu lakukan evaluasi.', 14, ['Menentukan target market', 'Merumuskan branding', 'Menyusun content strategy', 'Merancang campaign', 'Menentukan KPI dan melakukan evaluasi'], ['man-branding', 'man-digital-marketing', 'man-market-research', 'man-marketing-strategy', 'man-campaign-analysis']],
            ['Manajemen', 'Financial Health Analysis', 'financial-health-analysis', 'Menganalisis kondisi keuangan untuk menemukan masalah dan menyusun financial plan serta rekomendasi.', 'Diberikan kondisi atau laporan keuangan. Lakukan analisis, temukan masalah, susun financial plan, dan berikan rekomendasi.', 14, ['Membaca kondisi atau laporan keuangan', 'Melakukan financial analysis dan ratio analysis', 'Menemukan masalah utama', 'Menyusun financial plan', 'Memberikan rekomendasi keputusan keuangan'], ['man-financial-planning', 'man-financial-analysis', 'man-financial-ratios', 'man-investment-management', 'man-financial-decision-making']],
            ['Manajemen', 'Recruitment Strategy', 'recruitment-strategy', 'Menyusun strategi rekrutmen lengkap dari kebutuhan perusahaan sampai candidate scoring.', 'Diberikan kebutuhan perusahaan. Buat job profile, recruitment strategy, selection criteria, rancangan interview, dan candidate scoring.', 12, ['Menganalisis kebutuhan perusahaan', 'Membuat job profile', 'Menyusun recruitment strategy', 'Menetapkan selection criteria dan rancangan interview', 'Membuat candidate scoring'], ['man-recruitment-selection', 'man-candidate-selection', 'man-interview', 'man-performance-management', 'man-talent-management']],

            ['Teknik Informatika', 'Software Development Project', 'software-development-project', 'Menyelesaikan proyek pengembangan software dari requirement sampai dokumentasi.', 'Diberikan requirement. Lakukan analysis, design, coding, testing, dan dokumentasi.', 28, ['Menganalisis requirement', 'Membuat design solusi', 'Melakukan coding', 'Melakukan testing dan debugging', 'Menyusun dokumentasi'], ['ti-algorithms-data-structures', 'ti-data-structures', 'ti-object-oriented-programming', 'ti-software-engineering', 'ti-debugging']],
            ['Teknik Informatika', 'Company Network & Security Simulation', 'company-network-security-simulation', 'Merancang dan mensimulasikan jaringan perusahaan lengkap dengan konfigurasi, keamanan, troubleshooting, dan incident response.', 'Rancang network perusahaan, lakukan konfigurasi dan security hardening, uji troubleshooting, lalu susun incident response.', 22, ['Merancang topologi network perusahaan', 'Melakukan konfigurasi jaringan dan layanan sistem', 'Menerapkan kontrol security', 'Melakukan troubleshooting skenario gangguan', 'Menyusun incident response'], ['ti-computer-networks', 'ti-operating-systems', 'ti-network-troubleshooting', 'ti-cybersecurity', 'ti-system-administration']],
            ['Teknik Informatika', 'AI Predictive Project', 'ai-predictive-project', 'Membangun proyek prediksi berbasis AI dari dataset hingga insight.', 'Gunakan dataset untuk preprocessing, pemilihan model, training, evaluation, prediction, dan penarikan insight.', 22, ['Melakukan preprocessing dataset', 'Memilih model yang sesuai', 'Melakukan training', 'Melakukan model evaluation', 'Menghasilkan prediction dan insight'], ['ti-machine-learning', 'ti-data-science', 'ti-statistics', 'ti-model-evaluation', 'ti-computer-vision']],

            ['Sistem Komputer', 'Mini Computer Architecture Design', 'mini-computer-architecture-design', 'Merancang sistem komputer sederhana berdasarkan kebutuhan tertentu.', 'Rancang mini computer architecture yang menjelaskan komponen pemrosesan, logika digital, memory, dan peran microprocessor berdasarkan kebutuhan yang diberikan.', 16, ['Menentukan kebutuhan sistem', 'Menentukan komponen arsitektur utama', 'Menjelaskan alur pemrosesan', 'Merancang organisasi memory', 'Mendokumentasikan rancangan akhir'], ['sk-computer-architecture', 'sk-digital-logic', 'sk-processor', 'sk-memory', 'sk-microprocessor-microcontroller']],
            ['Sistem Komputer', 'Smart IoT System', 'smart-iot-system', 'Membangun rancangan Smart Office atau Smart Home dari sensor hingga automation.', 'Buat Smart IoT System, misalnya Smart Office atau Smart Home, dengan alur sensor, microcontroller, data, dashboard, dan automation.', 22, ['Menentukan skenario Smart Office atau Smart Home', 'Menghubungkan sensor dengan microcontroller', 'Mengirim dan mengolah data IoT', 'Menyajikan data pada dashboard', 'Menerapkan automation menggunakan actuator'], ['sk-microcontroller', 'sk-embedded-systems', 'sk-internet-of-things', 'sk-sensor-actuator-integration', 'sk-actuator']],
            ['Sistem Komputer', 'Secure Network Design', 'secure-network-design', 'Merancang jaringan perusahaan dengan IP/VLAN, firewall, security, dan monitoring.', 'Rancang jaringan perusahaan lengkap dengan IP/VLAN, firewall, kontrol security, dan monitoring.', 20, ['Merancang topologi jaringan perusahaan', 'Menyusun skema IP dan VLAN', 'Menerapkan aturan firewall', 'Menerapkan kontrol network security', 'Menyiapkan monitoring dan threat detection'], ['sk-computer-networks', 'sk-network-administration', 'sk-network-security', 'sk-firewall', 'sk-threat-detection']],

            ['Psikologi', 'Employee & Organizational Assessment', 'employee-organizational-assessment', 'Menganalisis kondisi karyawan dan organisasi untuk menemukan masalah serta memberikan rekomendasi.', 'Analisis kondisi karyawan atau organisasi, identifikasi masalah utama, lalu susun rekomendasi yang relevan.', 14, ['Mengumpulkan informasi kondisi karyawan atau organisasi', 'Menganalisis employee dan organizational behavior', 'Menggunakan work-style atau psychological assessment secara tepat', 'Menentukan masalah utama', 'Menyusun rekomendasi pengembangan organisasi'], ['psi-employee-behavior', 'psi-organizational-behavior', 'psi-work-style-assessment', 'psi-psychological-assessment', 'psi-organizational-development']],
            ['Psikologi', 'Counseling Case Simulation', 'counseling-case-simulation', 'Menyelesaikan simulasi kasus konseling melalui respons, strategi komunikasi, dan solusi yang tepat.', 'Diberikan kasus konseling. Tentukan respons, strategi komunikasi, dan solusi berdasarkan situasi.', 10, ['Memahami konteks kasus', 'Menentukan respons awal', 'Menerapkan active listening dan empathy', 'Menentukan strategi komunikasi', 'Menyusun solusi atau langkah tindak lanjut'], ['psi-interpersonal-communication', 'psi-counseling-skills', 'psi-empathy', 'psi-emotional-intelligence', 'psi-counseling-scenario']],
            ['Psikologi', 'Mini Psychological Research', 'mini-psychological-research', 'Melakukan mini research psikologi dari perumusan masalah sampai kesimpulan.', 'Tentukan masalah penelitian, buat instrumen, lakukan survey atau interview, analisis data, lalu tarik kesimpulan.', 18, ['Menentukan masalah dan metode penelitian', 'Membuat instrumen', 'Melakukan survey, interview, atau observation', 'Melakukan data analysis', 'Menarik kesimpulan'], ['psi-research-methodology', 'psi-interview-observation', 'psi-observation', 'psi-survey-data-analysis', 'psi-data-analysis']],

            ['Ilmu Komunikasi', 'Crisis Communication Simulation', 'crisis-communication-simulation', 'Menangani simulasi krisis perusahaan melalui press statement, media response, social media response, dan crisis strategy.', 'Diberikan kasus krisis perusahaan. Buat press statement, media response, social media response, dan crisis strategy.', 12, ['Menganalisis kasus krisis', 'Membuat press statement', 'Menyiapkan media response', 'Menyiapkan social media response', 'Menyusun crisis strategy dan pengelolaan reputasi'], ['ikom-media-relations', 'ikom-corporate-communication', 'ikom-crisis-communication', 'ikom-public-communication', 'ikom-reputation-management']],
            ['Ilmu Komunikasi', 'News Reporting Project', 'news-reporting-project', 'Membuat news report dari topik melalui research, interview, penulisan berita, dan fact checking.', 'Diberikan topik. Lakukan research dan interview, verifikasi fakta, tulis berita, lalu susun news report.', 14, ['Melakukan research topik', 'Melakukan interview', 'Melakukan fact checking', 'Menulis berita', 'Menyusun news report sesuai etika jurnalistik'], ['ikom-news-writing', 'ikom-journalistic-interview', 'ikom-news-reporting', 'ikom-fact-checking', 'ikom-journalistic-ethics']],
            ['Ilmu Komunikasi', 'Digital Content Campaign', 'digital-content-campaign', 'Merancang kampanye konten digital dari audience sampai evaluasi performa.', 'Tentukan audience, susun content strategy dan content calendar, buat konten, lalu evaluasi performanya.', 14, ['Menentukan audience', 'Menyusun content strategy', 'Membuat content calendar', 'Membuat konten digital', 'Mengevaluasi performa konten'], ['ikom-content-creation', 'ikom-social-media-management', 'ikom-video-production', 'ikom-content-strategy', 'ikom-audience-analysis']],
        ];

        $programNames = array_values(
            array_unique(
                array_map(
                    fn (array $definition) => $definition[0],
                    $definitions,
                ),
            ),
        );

        $careers = Career::query()
            ->whereIn('name', $programNames)
            ->get()
            ->keyBy('name');

        $skills = Skill::query()
            ->get()
            ->keyBy('slug');

        $canonicalSlugs = [];

        foreach ($definitions as [
            $careerName,
            $title,
            $slug,
            $summary,
            $problemStatement,
            $estimatedHours,
            $minimumFeatures,
            $skillSlugs,
        ]) {
            $career = $careers->get($careerName);

            if (! $career) {
                continue;
            }

            $project = PortfolioProject::updateOrCreate(
                ['slug' => $slug],
                [
                    'career_id' => $career->id,
                    'title' => $title,
                    'summary' => $summary,
                    'problem_statement' => $problemStatement,
                    'difficulty' => 'Menengah',
                    'minimum_features' => $minimumFeatures,
                    'stretch_features' => [],
                    'completion_criteria' => array_map(
                        fn (string $feature) => $feature.' selesai dan dapat diverifikasi.',
                        $minimumFeatures,
                    ),
                    'estimated_hours' => $estimatedHours,
                ],
            );

            $sync = [];

            foreach ($skillSlugs as $skillSlug) {
                $skill = $skills->get($skillSlug);

                if (! $skill) {
                    continue;
                }

                $sync[$skill->id] = [
                    'required_level' => 65,
                    'weight' => 1.00,
                ];
            }

            $project->skills()->sync($sync);
            $canonicalSlugs[] = $project->slug;
        }

        $academicCareerIds = $careers
            ->pluck('id')
            ->all();

        if ($academicCareerIds !== []) {
            $legacyProjects = PortfolioProject::query()
                ->whereIn('career_id', $academicCareerIds)
                ->whereNotIn('slug', $canonicalSlugs);

            if (
                (clone $legacyProjects)
                    ->whereHas('userProjects')
                    ->exists()
            ) {
                throw new \RuntimeException(
                    'Masih ada progres pengguna pada proyek lama. Pindahkan atau hapus progres tersebut sebelum membersihkan proyek non-PDF.',
                );
            }

            $legacyProjects->delete();
        }
    }
}
