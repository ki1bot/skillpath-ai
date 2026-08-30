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
                    'SQL & data processing',
                    'Spreadsheet analysis',
                    'Business Intelligence',
                    'Data visualization',
                    'Scenario-based data analysis',
                ],
            },
            {
                name: 'Pengembangan Sistem',
                skills: [
                    'Database',
                    'Web development',
                    'System analysis',
                    'ERD/UML',
                    'Problem solving',
                ],
            },
            {
                name: 'UI/UX',
                skills: [
                    'UI design',
                    'Wireframing',
                    'Prototyping',
                    'User research',
                    'Usability',
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
                skills: [
                    'Branding',
                    'Digital marketing',
                    'Market research',
                    'Marketing strategy',
                    'Campaign analysis',
                ],
            },
            {
                name: 'Keuangan',
                skills: [
                    'Financial planning',
                    'Financial analysis',
                    'Financial ratios',
                    'Investment basics',
                    'Financial decision making',
                ],
            },
            {
                name: 'Human Resources',
                skills: [
                    'Recruitment',
                    'Candidate selection',
                    'Interview',
                    'Performance management',
                    'Talent management',
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
                name: 'Pemrograman & Rekayasa Perangkat Lunak',
                skills: [
                    'Algoritma',
                    'Data structure',
                    'OOP',
                    'Software engineering',
                    'Debugging',
                ],
            },
            {
                name: 'Jaringan & Sistem Komputer',
                skills: [
                    'Computer networks',
                    'Operating systems',
                    'Network troubleshooting',
                    'Cybersecurity',
                    'System administration',
                ],
            },
            {
                name: 'Artificial Intelligence',
                skills: [
                    'Machine learning',
                    'Data science',
                    'Statistics',
                    'Model evaluation',
                    'Computer vision',
                ],
            },
        ],
    },
    {
        name: 'Sistem Komputer',
        description:
            'Untuk kamu yang ingin memahami perangkat komputasi dari arsitektur digital, embedded system dan IoT, hingga jaringan dan keamanan.',
        areas: [
            {
                name: 'Arsitektur & Organisasi Komputer',
                skills: [
                    'Computer architecture',
                    'Digital logic',
                    'Processor',
                    'Memory',
                    'Microprocessor',
                ],
            },
            {
                name: 'Embedded System & IoT',
                skills: [
                    'Microcontroller',
                    'Embedded system',
                    'IoT',
                    'Sensor',
                    'Actuator',
                ],
            },
            {
                name: 'Jaringan & Keamanan Komputer',
                skills: [
                    'Networking',
                    'Network administration',
                    'Network security',
                    'Firewall',
                    'Threat detection',
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
                name: 'Psikologi Industri & Organisasi',
                skills: [
                    'Employee behavior',
                    'Organizational behavior',
                    'Work-style assessment',
                    'Psychological assessment',
                    'Organizational development',
                ],
            },
            {
                name: 'Konseling',
                skills: [
                    'Communication',
                    'Active listening',
                    'Empathy',
                    'Emotional intelligence',
                    'Counseling scenario',
                ],
            },
            {
                name: 'Penelitian Psikologi',
                skills: [
                    'Research methodology',
                    'Interview',
                    'Observation',
                    'Survey',
                    'Data analysis',
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
                    'Media relations',
                    'Corporate communication',
                    'Crisis communication',
                    'Public communication',
                    'Reputation management',
                ],
            },
            {
                name: 'Jurnalistik',
                skills: [
                    'News writing',
                    'Interview',
                    'News reporting',
                    'Fact checking',
                    'Journalistic ethics',
                ],
            },
            {
                name: 'Digital Media',
                skills: [
                    'Content creation',
                    'Social media',
                    'Video production',
                    'Content strategy',
                    'Audience analysis',
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
