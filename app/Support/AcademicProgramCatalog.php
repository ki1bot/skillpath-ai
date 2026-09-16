<?php

namespace App\Support;

final class AcademicProgramCatalog
{
    private const PROGRAMS = [
        'Sistem Informasi' => [
            'slug' => 'sistem-informasi',
            'tagline' => 'Belajar menghubungkan data, proses bisnis, sistem, dan kebutuhan pengguna.',
            'description' => 'Di Sistem Informasi, kemampuanmu dipetakan melalui Analisis Data, Pengembangan Sistem, dan UI/UX. Setiap bidang memiliki tiga kemampuan, sehingga ada sembilan kemampuan akademik yang digunakan pada Assesment dan jalur belajar.',
            'difficulty' => 'Menengah',
            'accent' => '#79D7FF',
            'areas' => [
                [
                    'name' => 'Analisis Data',
                    'skills' => [
                        ['slug' => 'si-sql-data-processing', 'name' => 'SQL dan Pengolahan Data', 'difficulty' => 'Menengah'],
                        ['slug' => 'si-spreadsheet-data-analysis', 'name' => 'Spreadsheet dan Analisis Data', 'difficulty' => 'Dasar'],
                        ['slug' => 'si-business-intelligence-data-visualization', 'name' => 'Business Intelligence dan Visualisasi Data', 'difficulty' => 'Menengah'],
                    ],
                ],
                [
                    'name' => 'Pengembangan Sistem',
                    'skills' => [
                        ['slug' => 'si-database-management', 'name' => 'Database Management', 'difficulty' => 'Menengah'],
                        ['slug' => 'si-web-development', 'name' => 'Web Development', 'difficulty' => 'Menengah'],
                        ['slug' => 'si-system-analysis-design', 'name' => 'System Analysis and Design', 'difficulty' => 'Menengah'],
                    ],
                ],
                [
                    'name' => 'UI/UX',
                    'skills' => [
                        ['slug' => 'si-ui-design', 'name' => 'UI Design', 'difficulty' => 'Dasar'],
                        ['slug' => 'si-wireframing-prototyping', 'name' => 'Wireframing dan Prototyping', 'difficulty' => 'Dasar'],
                        ['slug' => 'si-user-research', 'name' => 'User Research', 'difficulty' => 'Menengah'],
                    ],
                ],
            ],
        ],
        'Manajemen' => [
            'slug' => 'manajemen',
            'tagline' => 'Belajar memahami pasar, mengelola keuangan, dan mengembangkan orang di dalam organisasi.',
            'description' => 'Di Manajemen, kemampuanmu dipetakan melalui Marketing, Keuangan, dan Human Resources. Setiap bidang memiliki tiga kemampuan, sehingga ada sembilan kemampuan akademik yang digunakan pada Assesment dan jalur belajar.',
            'difficulty' => 'Menengah',
            'accent' => '#FFD95A',
            'areas' => [
                [
                    'name' => 'Marketing',
                    'skills' => [
                        ['slug' => 'man-branding', 'name' => 'Branding', 'difficulty' => 'Menengah'],
                        ['slug' => 'man-digital-marketing', 'name' => 'Digital Marketing', 'difficulty' => 'Menengah'],
                        ['slug' => 'man-market-research', 'name' => 'Market Research', 'difficulty' => 'Menengah'],
                    ],
                ],
                [
                    'name' => 'Keuangan',
                    'skills' => [
                        ['slug' => 'man-financial-planning', 'name' => 'Financial Planning', 'difficulty' => 'Menengah'],
                        ['slug' => 'man-financial-analysis', 'name' => 'Financial Analysis', 'difficulty' => 'Menengah'],
                        ['slug' => 'man-investment-management', 'name' => 'Investment Management', 'difficulty' => 'Dasar'],
                    ],
                ],
                [
                    'name' => 'Human Resources',
                    'skills' => [
                        ['slug' => 'man-recruitment-selection', 'name' => 'Recruitment and Selection', 'difficulty' => 'Menengah'],
                        ['slug' => 'man-performance-management', 'name' => 'Performance Management', 'difficulty' => 'Menengah'],
                        ['slug' => 'man-talent-management', 'name' => 'Talent Management', 'difficulty' => 'Menengah'],
                    ],
                ],
            ],
        ],
        'Teknik Informatika' => [
            'slug' => 'teknik-informatika',
            'tagline' => 'Bangun dasar pemrograman, pahami sistem komputer, lalu kenali penerapan kecerdasan buatan.',
            'description' => 'Di Teknik Informatika, kemampuanmu dipetakan melalui Pemrograman dan Rekayasa Perangkat Lunak, Jaringan dan Sistem Komputer, serta Artificial Intelligence. Setiap bidang memiliki tiga kemampuan, sehingga ada sembilan kemampuan akademik yang digunakan pada Assesment dan jalur belajar.',
            'difficulty' => 'Menengah',
            'accent' => '#C7FF5E',
            'areas' => [
                [
                    'name' => 'Pemrograman dan Rekayasa Perangkat Lunak',
                    'skills' => [
                        ['slug' => 'ti-algorithms-data-structures', 'name' => 'Algoritma dan Struktur Data', 'difficulty' => 'Menengah'],
                        ['slug' => 'ti-object-oriented-programming', 'name' => 'Object-Oriented Programming', 'difficulty' => 'Menengah'],
                        ['slug' => 'ti-software-engineering', 'name' => 'Software Engineering', 'difficulty' => 'Menengah'],
                    ],
                ],
                [
                    'name' => 'Jaringan dan Sistem Komputer',
                    'skills' => [
                        ['slug' => 'ti-computer-networks', 'name' => 'Computer Networks', 'difficulty' => 'Menengah'],
                        ['slug' => 'ti-operating-systems', 'name' => 'Operating Systems', 'difficulty' => 'Menengah'],
                        ['slug' => 'ti-cybersecurity', 'name' => 'Cybersecurity', 'difficulty' => 'Menengah'],
                    ],
                ],
                [
                    'name' => 'Artificial Intelligence',
                    'skills' => [
                        ['slug' => 'ti-machine-learning', 'name' => 'Machine Learning', 'difficulty' => 'Menengah'],
                        ['slug' => 'ti-data-science', 'name' => 'Data Science', 'difficulty' => 'Menengah'],
                        ['slug' => 'ti-computer-vision', 'name' => 'Computer Vision', 'difficulty' => 'Menengah'],
                    ],
                ],
            ],
        ],
        'Sistem Komputer' => [
            'slug' => 'sistem-komputer',
            'tagline' => 'Pahami arsitektur komputer, embedded system, IoT, jaringan, dan keamanan komputer.',
            'description' => 'Di Sistem Komputer, kemampuanmu dipetakan melalui Arsitektur dan Organisasi Komputer, Embedded System dan Internet of Things, serta Jaringan dan Keamanan Komputer. Setiap bidang memiliki tiga kemampuan, sehingga ada sembilan kemampuan akademik yang digunakan pada Assesment dan jalur belajar.',
            'difficulty' => 'Menengah',
            'accent' => '#FF9F68',
            'areas' => [
                [
                    'name' => 'Arsitektur dan Organisasi Komputer',
                    'skills' => [
                        ['slug' => 'sk-computer-architecture', 'name' => 'Computer Architecture', 'difficulty' => 'Menengah'],
                        ['slug' => 'sk-digital-logic', 'name' => 'Digital Logic', 'difficulty' => 'Dasar'],
                        ['slug' => 'sk-microprocessor-microcontroller', 'name' => 'Microprocessor and Microcontroller', 'difficulty' => 'Menengah'],
                    ],
                ],
                [
                    'name' => 'Embedded System dan Internet of Things',
                    'skills' => [
                        ['slug' => 'sk-embedded-systems', 'name' => 'Embedded Systems', 'difficulty' => 'Menengah'],
                        ['slug' => 'sk-internet-of-things', 'name' => 'Internet of Things', 'difficulty' => 'Menengah'],
                        ['slug' => 'sk-sensor-actuator-integration', 'name' => 'Sensor and Actuator Integration', 'difficulty' => 'Menengah'],
                    ],
                ],
                [
                    'name' => 'Jaringan dan Keamanan Komputer',
                    'skills' => [
                        ['slug' => 'sk-computer-networks', 'name' => 'Computer Networks', 'difficulty' => 'Menengah'],
                        ['slug' => 'sk-network-administration', 'name' => 'Network Administration', 'difficulty' => 'Menengah'],
                        ['slug' => 'sk-network-security', 'name' => 'Network Security', 'difficulty' => 'Menengah'],
                    ],
                ],
            ],
        ],
        'Psikologi' => [
            'slug' => 'psikologi',
            'tagline' => 'Pahami perilaku manusia di organisasi, dalam konseling, dan melalui penelitian.',
            'description' => 'Di Psikologi, kemampuanmu dipetakan melalui Psikologi Industri dan Organisasi, Konseling, dan Penelitian Psikologi. Setiap bidang memiliki tiga kemampuan, sehingga ada sembilan kemampuan akademik yang digunakan pada Assesment dan jalur belajar.',
            'difficulty' => 'Menengah',
            'accent' => '#FF8FAB',
            'areas' => [
                [
                    'name' => 'Psikologi Industri dan Organisasi',
                    'skills' => [
                        ['slug' => 'psi-employee-behavior', 'name' => 'Employee Behavior', 'difficulty' => 'Menengah'],
                        ['slug' => 'psi-organizational-development', 'name' => 'Organizational Development', 'difficulty' => 'Menengah'],
                        ['slug' => 'psi-psychological-assessment', 'name' => 'Psychological Assessment', 'difficulty' => 'Menengah'],
                    ],
                ],
                [
                    'name' => 'Konseling',
                    'skills' => [
                        ['slug' => 'psi-counseling-skills', 'name' => 'Counseling Skills', 'difficulty' => 'Menengah'],
                        ['slug' => 'psi-interpersonal-communication', 'name' => 'Interpersonal Communication', 'difficulty' => 'Dasar'],
                        ['slug' => 'psi-emotional-intelligence', 'name' => 'Emotional Intelligence', 'difficulty' => 'Menengah'],
                    ],
                ],
                [
                    'name' => 'Penelitian Psikologi',
                    'skills' => [
                        ['slug' => 'psi-research-methodology', 'name' => 'Research Methodology', 'difficulty' => 'Menengah'],
                        ['slug' => 'psi-interview-observation', 'name' => 'Interview dan Observation', 'difficulty' => 'Menengah'],
                        ['slug' => 'psi-survey-data-analysis', 'name' => 'Survey dan Data Analysis', 'difficulty' => 'Menengah'],
                    ],
                ],
            ],
        ],
        'Ilmu Komunikasi' => [
            'slug' => 'ilmu-komunikasi',
            'tagline' => 'Pelajari cara membangun hubungan, menyampaikan berita, dan membuat konten digital.',
            'description' => 'Di Ilmu Komunikasi, kemampuanmu dipetakan melalui Public Relations, Jurnalistik, dan Digital Media. Setiap bidang memiliki tiga kemampuan, sehingga ada sembilan kemampuan akademik yang digunakan pada Assesment dan jalur belajar.',
            'difficulty' => 'Menengah',
            'accent' => '#C4B5FD',
            'areas' => [
                [
                    'name' => 'Public Relations',
                    'skills' => [
                        ['slug' => 'ikom-media-relations', 'name' => 'Media Relations', 'difficulty' => 'Menengah'],
                        ['slug' => 'ikom-corporate-communication', 'name' => 'Corporate Communication', 'difficulty' => 'Menengah'],
                        ['slug' => 'ikom-crisis-communication', 'name' => 'Crisis Communication', 'difficulty' => 'Menengah'],
                    ],
                ],
                [
                    'name' => 'Jurnalistik',
                    'skills' => [
                        ['slug' => 'ikom-news-writing', 'name' => 'News Writing', 'difficulty' => 'Dasar'],
                        ['slug' => 'ikom-journalistic-interview', 'name' => 'Journalistic Interview', 'difficulty' => 'Menengah'],
                        ['slug' => 'ikom-news-reporting', 'name' => 'News Reporting', 'difficulty' => 'Menengah'],
                    ],
                ],
                [
                    'name' => 'Digital Media',
                    'skills' => [
                        ['slug' => 'ikom-content-creation', 'name' => 'Content Creation', 'difficulty' => 'Menengah'],
                        ['slug' => 'ikom-social-media-management', 'name' => 'Social Media Management', 'difficulty' => 'Menengah'],
                        ['slug' => 'ikom-video-production', 'name' => 'Video Production', 'difficulty' => 'Menengah'],
                    ],
                ],
            ],
        ],
    ];

    public static function programs(): array
    {
        return self::PROGRAMS;
    }

    public static function program(string $studyProgram): ?array
    {
        return self::PROGRAMS[$studyProgram] ?? null;
    }

    public static function skillDefinitions(?string $studyProgram = null): array
    {
        $programs = $studyProgram === null
            ? self::PROGRAMS
            : array_filter(
                self::PROGRAMS,
                fn (string $name): bool => $name === $studyProgram,
                ARRAY_FILTER_USE_KEY,
            );

        $definitions = [];

        foreach ($programs as $programName => $program) {
            foreach ($program['areas'] as $area) {
                foreach ($area['skills'] as $skill) {
                    $definitions[] = [
                        'study_program' => $programName,
                        'slug' => $skill['slug'],
                        'name' => $skill['name'],
                        'category' => $area['name'],
                        'description' => 'Memahami konsep dan penerapan '
                            .$skill['name']
                            .' dalam konteks '
                            .$area['name']
                            .'.',
                        'difficulty' => $skill['difficulty'],
                    ];
                }
            }
        }

        return $definitions;
    }

    public static function skillSlugs(string $studyProgram): array
    {
        $program = self::program($studyProgram);

        if ($program === null) {
            return [];
        }

        $slugs = [];

        foreach ($program['areas'] as $area) {
            foreach ($area['skills'] as $skill) {
                $slugs[] = $skill['slug'];
            }
        }

        return $slugs;
    }

    public static function allSkillSlugs(): array
    {
        $slugs = [];

        foreach (array_keys(self::PROGRAMS) as $studyProgram) {
            $slugs = [
                ...$slugs,
                ...self::skillSlugs($studyProgram),
            ];
        }

        return $slugs;
    }

    public static function areaSkillSlugs(
        string $studyProgram,
        string $areaName,
    ): array {
        $program = self::program($studyProgram);

        if ($program === null) {
            return [];
        }

        foreach ($program['areas'] as $area) {
            if ($area['name'] !== $areaName) {
                continue;
            }

            return array_map(
                fn (array $skill): string => $skill['slug'],
                $area['skills'],
            );
        }

        return [];
    }
}
