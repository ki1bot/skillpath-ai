<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\PortfolioProject;
use App\Models\Skill;
use App\Support\AcademicProgramCatalog;
use App\Support\SkillPathScoringPolicy;
use Illuminate\Database\Seeder;
use RuntimeException;

class AcademicPortfolioProjectSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            [
                'Sistem Informasi',
                'Analisis Data',
                'Sales & Business Intelligence Dashboard',
                'sales-business-intelligence-dashboard',
                'Membangun dashboard Business Intelligence dari dataset penjualan perusahaan untuk menghasilkan insight dan rekomendasi bisnis.',
                'Diberikan dataset perusahaan. Lakukan cleaning data, analisis, pembuatan dashboard, penarikan insight, dan penyusunan rekomendasi bisnis.',
                16,
                [
                    'Membersihkan dataset perusahaan',
                    'Melakukan analisis data penjualan',
                    'Membangun dashboard Business Intelligence',
                    'Menuliskan insight utama dari data',
                    'Menyusun rekomendasi bisnis berdasarkan hasil analisis',
                ],
            ],
            [
                'Sistem Informasi',
                'Pengembangan Sistem',
                'Build Mini Information System',
                'build-mini-information-system',
                'Membangun sistem informasi mini dari sebuah studi kasus dengan database, UI, backend, dan fitur utama yang dapat digunakan.',
                'Diberikan studi kasus. Rancang dan bangun sistem informasi lengkap dengan database, UI, backend, dan fitur utama.',
                24,
                [
                    'Menganalisis kebutuhan dari studi kasus',
                    'Membuat rancangan data dan alur sistem',
                    'Membangun database',
                    'Membangun UI dan backend',
                    'Menyelesaikan fitur utama dan menguji alur sistem',
                ],
            ],
            [
                'Sistem Informasi',
                'UI/UX',
                'Redesign Digital Product',
                'redesign-digital-product',
                'Merancang ulang produk digital berdasarkan masalah pengguna melalui riset, user flow, wireframe, prototype, dan validasi desain.',
                'Diberikan masalah pada sebuah produk digital. Lakukan user research, susun user flow, lanjutkan ke wireframe dan prototype, lalu validasi rancangan.',
                18,
                [
                    'Melakukan user research',
                    'Menyusun user flow',
                    'Membuat wireframe',
                    'Membuat prototype',
                    'Mendokumentasikan hasil validasi dan perbaikan desain',
                ],
            ],
            [
                'Manajemen',
                'Marketing',
                'Digital Marketing Campaign',
                'digital-marketing-campaign',
                'Merancang kampanye digital dari pemahaman pasar hingga evaluasi hasil kampanye.',
                'Tentukan target market, susun branding dan strategi digital marketing, gunakan hasil market research, lalu evaluasi rancangan kampanye.',
                14,
                [
                    'Menentukan target market',
                    'Merumuskan branding',
                    'Menyusun strategi digital marketing',
                    'Menggunakan market research sebagai dasar keputusan',
                    'Menyusun evaluasi hasil kampanye',
                ],
            ],
            [
                'Manajemen',
                'Keuangan',
                'Financial Health Analysis',
                'financial-health-analysis',
                'Menganalisis kondisi keuangan untuk menemukan masalah dan menyusun rencana serta rekomendasi keuangan.',
                'Diberikan kondisi atau laporan keuangan. Lakukan financial analysis, susun financial plan, dan berikan pertimbangan investment management yang relevan.',
                14,
                [
                    'Membaca kondisi atau laporan keuangan',
                    'Melakukan financial analysis',
                    'Menemukan masalah utama',
                    'Menyusun financial plan',
                    'Memberikan rekomendasi keputusan investasi atau pengelolaan keuangan',
                ],
            ],
            [
                'Manajemen',
                'Human Resources',
                'Recruitment Strategy',
                'recruitment-strategy',
                'Menyusun strategi pengelolaan sumber daya manusia dari rekrutmen sampai pengembangan talenta.',
                'Diberikan kebutuhan perusahaan. Susun recruitment and selection, performance management, dan talent management yang saling terhubung.',
                12,
                [
                    'Menganalisis kebutuhan perusahaan',
                    'Menyusun recruitment and selection',
                    'Menentukan kriteria penilaian kandidat',
                    'Menyusun pendekatan performance management',
                    'Menyusun rencana talent management',
                ],
            ],
            [
                'Teknik Informatika',
                'Pemrograman dan Rekayasa Perangkat Lunak',
                'Software Development Project',
                'software-development-project',
                'Menyelesaikan proyek pengembangan software dari requirement sampai dokumentasi.',
                'Diberikan requirement. Gunakan algoritma dan struktur data, OOP, serta praktik software engineering untuk membangun solusi.',
                28,
                [
                    'Menganalisis requirement',
                    'Merancang algoritma dan struktur data',
                    'Menerapkan Object-Oriented Programming',
                    'Mengembangkan dan menguji software',
                    'Menyusun dokumentasi',
                ],
            ],
            [
                'Teknik Informatika',
                'Jaringan dan Sistem Komputer',
                'Company Network & Security Simulation',
                'company-network-security-simulation',
                'Merancang dan mensimulasikan jaringan perusahaan dengan perhatian pada sistem operasi dan keamanan.',
                'Rancang network perusahaan, tentukan kebutuhan operating system, terapkan kontrol cybersecurity, lalu dokumentasikan hasil pengujian.',
                22,
                [
                    'Merancang topologi jaringan perusahaan',
                    'Melakukan konfigurasi jaringan',
                    'Menentukan kebutuhan operating system',
                    'Menerapkan kontrol cybersecurity',
                    'Menguji dan mendokumentasikan hasil simulasi',
                ],
            ],
            [
                'Teknik Informatika',
                'Artificial Intelligence',
                'AI Predictive Project',
                'ai-predictive-project',
                'Membangun proyek AI dari pengolahan data hingga penerapan model.',
                'Gunakan dataset untuk data science, bangun model machine learning, lalu terapkan atau evaluasi pendekatan computer vision bila sesuai dengan kasus.',
                22,
                [
                    'Menyiapkan dan memahami dataset',
                    'Melakukan proses data science',
                    'Memilih pendekatan machine learning',
                    'Melakukan training dan evaluasi model',
                    'Mendokumentasikan penerapan computer vision atau keluaran model',
                ],
            ],
            [
                'Sistem Komputer',
                'Arsitektur dan Organisasi Komputer',
                'Mini Computer Architecture Design',
                'mini-computer-architecture-design',
                'Merancang sistem komputer sederhana berdasarkan kebutuhan tertentu.',
                'Rancang mini computer architecture yang menjelaskan digital logic dan peran microprocessor serta microcontroller berdasarkan kebutuhan yang diberikan.',
                16,
                [
                    'Menentukan kebutuhan sistem',
                    'Menentukan komponen arsitektur utama',
                    'Menjelaskan digital logic yang digunakan',
                    'Menjelaskan peran microprocessor dan microcontroller',
                    'Mendokumentasikan rancangan akhir',
                ],
            ],
            [
                'Sistem Komputer',
                'Embedded System dan Internet of Things',
                'Smart IoT System',
                'smart-iot-system',
                'Membangun rancangan Smart Office atau Smart Home dari embedded system sampai integrasi sensor dan actuator.',
                'Buat Smart IoT System dengan embedded system, Internet of Things, serta sensor and actuator integration yang sesuai.',
                22,
                [
                    'Menentukan skenario Smart Office atau Smart Home',
                    'Membangun rancangan embedded system',
                    'Menghubungkan perangkat ke Internet of Things',
                    'Mengintegrasikan sensor',
                    'Mengintegrasikan actuator dan menguji alurnya',
                ],
            ],
            [
                'Sistem Komputer',
                'Jaringan dan Keamanan Komputer',
                'Secure Network Design',
                'secure-network-design',
                'Merancang jaringan perusahaan dengan administrasi dan keamanan jaringan yang terukur.',
                'Rancang jaringan perusahaan lengkap dengan computer networks, network administration, dan network security.',
                20,
                [
                    'Merancang topologi jaringan perusahaan',
                    'Menyusun skema alamat jaringan',
                    'Melakukan konfigurasi administrasi jaringan',
                    'Menerapkan kontrol network security',
                    'Menguji dan mendokumentasikan keamanan jaringan',
                ],
            ],
            [
                'Psikologi',
                'Psikologi Industri dan Organisasi',
                'Employee & Organizational Assessment',
                'employee-organizational-assessment',
                'Menganalisis kondisi karyawan dan organisasi untuk menemukan masalah serta memberikan rekomendasi.',
                'Analisis employee behavior, organizational development, dan penggunaan psychological assessment secara tepat dalam sebuah kasus organisasi.',
                14,
                [
                    'Mengumpulkan informasi kondisi karyawan atau organisasi',
                    'Menganalisis employee behavior',
                    'Mengidentifikasi kebutuhan organizational development',
                    'Menentukan penggunaan psychological assessment yang tepat',
                    'Menyusun rekomendasi',
                ],
            ],
            [
                'Psikologi',
                'Konseling',
                'Counseling Case Simulation',
                'counseling-case-simulation',
                'Menyelesaikan simulasi kasus konseling melalui keterampilan konseling, komunikasi interpersonal, dan kecerdasan emosional.',
                'Diberikan kasus konseling. Tentukan respons menggunakan counseling skills, interpersonal communication, dan emotional intelligence.',
                10,
                [
                    'Memahami konteks kasus',
                    'Menentukan respons awal',
                    'Menerapkan counseling skills',
                    'Menerapkan interpersonal communication',
                    'Menggunakan emotional intelligence dalam tindak lanjut',
                ],
            ],
            [
                'Psikologi',
                'Penelitian Psikologi',
                'Mini Psychological Research',
                'mini-psychological-research',
                'Melakukan mini research psikologi dari perumusan masalah sampai kesimpulan.',
                'Gunakan research methodology, interview dan observation, serta survey dan data analysis untuk menyelesaikan penelitian sederhana.',
                18,
                [
                    'Menentukan masalah dan metode penelitian',
                    'Menyusun instrumen',
                    'Melakukan interview dan observation',
                    'Melakukan survey dan data analysis',
                    'Menarik kesimpulan',
                ],
            ],
            [
                'Ilmu Komunikasi',
                'Public Relations',
                'Crisis Communication Simulation',
                'crisis-communication-simulation',
                'Menangani simulasi krisis perusahaan melalui hubungan media dan komunikasi korporat.',
                'Diberikan kasus krisis perusahaan. Gunakan media relations, corporate communication, dan crisis communication untuk menyusun respons.',
                12,
                [
                    'Menganalisis kasus krisis',
                    'Menyiapkan media relations',
                    'Menyusun corporate communication',
                    'Menyusun crisis communication',
                    'Mendokumentasikan strategi respons',
                ],
            ],
            [
                'Ilmu Komunikasi',
                'Jurnalistik',
                'News Reporting Project',
                'news-reporting-project',
                'Membuat news report dari topik melalui interview, penulisan berita, dan pelaporan.',
                'Diberikan topik. Lakukan journalistic interview, tulis berita, lalu susun news reporting yang jelas dan dapat diverifikasi.',
                14,
                [
                    'Melakukan riset topik',
                    'Melakukan journalistic interview',
                    'Menulis news writing',
                    'Menyusun news reporting',
                    'Memeriksa konsistensi informasi sebelum publikasi',
                ],
            ],
            [
                'Ilmu Komunikasi',
                'Digital Media',
                'Digital Content Campaign',
                'digital-content-campaign',
                'Merancang kampanye konten digital dari pembuatan konten sampai pengelolaan media sosial dan video.',
                'Tentukan audience, buat content creation plan, kelola social media, dan hasilkan video production yang mendukung kampanye.',
                14,
                [
                    'Menentukan audience',
                    'Menyusun rencana content creation',
                    'Membuat konten digital',
                    'Menyusun social media management',
                    'Membuat atau merancang video production',
                ],
            ],
        ];

        $programNames = array_values(
            array_unique(
                array_map(
                    fn (array $definition): string => $definition[0],
                    $definitions,
                ),
            ),
        );

        $careers = Career::query()
            ->whereIn(
                'name',
                $programNames,
            )
            ->get()
            ->keyBy('name');

        $skills = Skill::query()
            ->whereIn(
                'slug',
                AcademicProgramCatalog::allSkillSlugs(),
            )
            ->get()
            ->keyBy('slug');

        $canonicalSlugs = [];

        foreach ($definitions as [
            $careerName,
            $areaName,
            $title,
            $slug,
            $summary,
            $problemStatement,
            $estimatedHours,
            $minimumFeatures,
        ]) {
            $career = $careers->get(
                $careerName,
            );

            if (! $career) {
                throw new RuntimeException(
                    'Jurusan '.$careerName.' belum tersedia untuk proyek '.$title.'.',
                );
            }

            $project = PortfolioProject::updateOrCreate(
                [
                    'slug' => $slug,
                ],
                [
                    'career_id' => $career->id,
                    'title' => $title,
                    'summary' => $summary,
                    'problem_statement' => $problemStatement,
                    'difficulty' => 'Menengah',
                    'minimum_features' => $minimumFeatures,
                    'stretch_features' => [],
                    'completion_criteria' => array_map(
                        fn (string $feature): string => $feature.' selesai dan dapat diverifikasi.',
                        $minimumFeatures,
                    ),
                    'estimated_hours' => $estimatedHours,
                ],
            );

            $skillSlugs = AcademicProgramCatalog::areaSkillSlugs(
                $careerName,
                $areaName,
            );

            if (count($skillSlugs) !== 3) {
                throw new RuntimeException(
                    'Bidang '.$areaName.' pada jurusan '.$careerName.' harus memiliki tepat 3 skill.',
                );
            }

            $sync = [];

            foreach ($skillSlugs as $skillSlug) {
                $skill = $skills->get(
                    $skillSlug,
                );

                if (! $skill) {
                    throw new RuntimeException(
                        'Skill '.$skillSlug.' untuk proyek '.$title.' belum tersedia.',
                    );
                }

                $sync[$skill->id] = [
                    'required_level' => SkillPathScoringPolicy::FUNCTIONAL_LEVEL,
                    'weight' => SkillPathScoringPolicy::DEFAULT_PROJECT_WEIGHT,
                ];
            }

            $project
                ->skills()
                ->sync(
                    $sync,
                );

            $canonicalSlugs[] = $project->slug;
        }

        $academicCareerIds = $careers
            ->pluck('id')
            ->all();

        if ($academicCareerIds === []) {
            return;
        }

        $legacyProjects = PortfolioProject::query()
            ->whereIn(
                'career_id',
                $academicCareerIds,
            )
            ->whereNotIn(
                'slug',
                $canonicalSlugs,
            );

        if (
            (clone $legacyProjects)
                ->whereHas('userProjects')
                ->exists()
        ) {
            throw new RuntimeException(
                'Masih ada progres pengguna pada proyek lama. Pindahkan atau hapus progres tersebut sebelum membersihkan proyek lama.',
            );
        }

        $legacyProjects->delete();
    }
}
