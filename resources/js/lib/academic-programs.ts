export type StudyProgramName =
    | 'Sistem Informasi'
    | 'Manajemen'
    | 'Teknik Informatika'
    | 'Sistem Komputer'
    | 'Psikologi'
    | 'Ilmu Komunikasi';

export type StudyProgramArea = {
    name: string;
    skills: readonly string[];
};

export type StudyProgramDefinition = {
    name: StudyProgramName;
    description: string;
    areas: readonly StudyProgramArea[];
};

export const STUDY_PROGRAMS: readonly StudyProgramDefinition[] = [
    {
        name: 'Sistem Informasi',
        description:
            'Untuk kamu yang ingin memahami bagaimana data, proses bisnis, dan teknologi disatukan menjadi sistem yang berguna.',
        areas: [
            {
                name: 'Analisis Data',
                skills: [
                    'SQL dan Pengolahan Data',
                    'Spreadsheet dan Analisis Data',
                    'Business Intelligence dan Visualisasi Data',
                ],
            },
            {
                name: 'Pengembangan Sistem',
                skills: [
                    'Database Management',
                    'Web Development',
                    'System Analysis and Design',
                ],
            },
            {
                name: 'UI/UX',
                skills: [
                    'UI Design',
                    'Wireframing dan Prototyping',
                    'User Research',
                ],
            },
        ],
    },
    {
        name: 'Manajemen',
        description:
            'Untuk kamu yang ingin memahami pemasaran, keputusan keuangan, dan pengelolaan orang di dalam organisasi.',
        areas: [
            {
                name: 'Marketing',
                skills: ['Branding', 'Digital Marketing', 'Market Research'],
            },
            {
                name: 'Keuangan',
                skills: [
                    'Financial Planning',
                    'Financial Analysis',
                    'Investment Management',
                ],
            },
            {
                name: 'Human Resources',
                skills: [
                    'Recruitment and Selection',
                    'Performance Management',
                    'Talent Management',
                ],
            },
        ],
    },
    {
        name: 'Teknik Informatika',
        description:
            'Untuk kamu yang banyak belajar pemrograman, sistem komputer, jaringan, dan kecerdasan buatan.',
        areas: [
            {
                name: 'Pemrograman dan Rekayasa Perangkat Lunak',
                skills: [
                    'Algoritma dan Struktur Data',
                    'Object-Oriented Programming',
                    'Software Engineering',
                ],
            },
            {
                name: 'Jaringan dan Sistem Komputer',
                skills: [
                    'Computer Networks',
                    'Operating Systems',
                    'Cybersecurity',
                ],
            },
            {
                name: 'Artificial Intelligence',
                skills: ['Machine Learning', 'Data Science', 'Computer Vision'],
            },
        ],
    },
    {
        name: 'Sistem Komputer',
        description:
            'Untuk kamu yang ingin memahami perangkat komputasi dari arsitektur digital, embedded system dan IoT, hingga jaringan dan keamanan.',
        areas: [
            {
                name: 'Arsitektur dan Organisasi Komputer',
                skills: [
                    'Computer Architecture',
                    'Digital Logic',
                    'Microprocessor and Microcontroller',
                ],
            },
            {
                name: 'Embedded System dan Internet of Things',
                skills: [
                    'Embedded Systems',
                    'Internet of Things',
                    'Sensor and Actuator Integration',
                ],
            },
            {
                name: 'Jaringan dan Keamanan Komputer',
                skills: [
                    'Computer Networks',
                    'Network Administration',
                    'Network Security',
                ],
            },
        ],
    },
    {
        name: 'Psikologi',
        description:
            'Untuk kamu yang ingin memahami perilaku manusia, proses konseling, serta cara melakukan penelitian psikologi.',
        areas: [
            {
                name: 'Psikologi Industri dan Organisasi',
                skills: [
                    'Employee Behavior',
                    'Organizational Development',
                    'Psychological Assessment',
                ],
            },
            {
                name: 'Konseling',
                skills: [
                    'Counseling Skills',
                    'Interpersonal Communication',
                    'Emotional Intelligence',
                ],
            },
            {
                name: 'Penelitian Psikologi',
                skills: [
                    'Research Methodology',
                    'Interview dan Observation',
                    'Survey dan Data Analysis',
                ],
            },
        ],
    },
    {
        name: 'Ilmu Komunikasi',
        description:
            'Untuk kamu yang ingin mengembangkan kemampuan komunikasi publik, jurnalistik, dan produksi media digital.',
        areas: [
            {
                name: 'Public Relations',
                skills: [
                    'Media Relations',
                    'Corporate Communication',
                    'Crisis Communication',
                ],
            },
            {
                name: 'Jurnalistik',
                skills: [
                    'News Writing',
                    'Journalistic Interview',
                    'News Reporting',
                ],
            },
            {
                name: 'Digital Media',
                skills: [
                    'Content Creation',
                    'Social Media Management',
                    'Video Production',
                ],
            },
        ],
    },
];

export function getStudyProgramDefinition(
    name?: string | null,
): StudyProgramDefinition | undefined {
    return STUDY_PROGRAMS.find((program) => program.name === name);
}
