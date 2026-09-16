export type AcademicProgramArea = {
    name: string;
    skills: string[];
};

export type AcademicProgramDefinition = {
    name: string;
    areas: AcademicProgramArea[];
};

export const academicPrograms: AcademicProgramDefinition[] = [
    {
        name: 'Sistem Informasi',
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
    name: string,
): AcademicProgramDefinition | undefined {
    return academicPrograms.find((program) => program.name === name);
}
