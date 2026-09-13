export interface ProjectCatalogEntry {
    program: string;
    focus: string;
    skills: string[];
}

export const projectCatalog: Record<string, ProjectCatalogEntry> = {
    'sales-business-intelligence-dashboard': {
        program: 'Sistem Informasi',
        focus: 'Analisis Data',
        skills: [
            'SQL dan Pengolahan Data',
            'Spreadsheet dan Analisis Data',
            'Business Intelligence dan Visualisasi Data',
        ],
    },
    'build-mini-information-system': {
        program: 'Sistem Informasi',
        focus: 'Pengembangan Sistem',
        skills: [
            'Database Management',
            'Web Development',
            'System Analysis and Design',
        ],
    },
    'redesign-digital-product': {
        program: 'Sistem Informasi',
        focus: 'UI/UX',
        skills: ['UI Design', 'Wireframing dan Prototyping', 'User Research'],
    },
    'digital-marketing-campaign': {
        program: 'Manajemen',
        focus: 'Marketing',
        skills: ['Branding', 'Digital Marketing', 'Market Research'],
    },
    'financial-health-analysis': {
        program: 'Manajemen',
        focus: 'Keuangan',
        skills: [
            'Financial Planning',
            'Financial Analysis',
            'Investment Management',
        ],
    },
    'recruitment-strategy': {
        program: 'Manajemen',
        focus: 'Human Resources',
        skills: [
            'Recruitment and Selection',
            'Performance Management',
            'Talent Management',
        ],
    },
    'software-development-project': {
        program: 'Teknik Informatika',
        focus: 'Pemrograman dan Rekayasa Perangkat Lunak',
        skills: [
            'Algoritma dan Struktur Data',
            'Object-Oriented Programming',
            'Software Engineering',
        ],
    },
    'company-network-security-simulation': {
        program: 'Teknik Informatika',
        focus: 'Jaringan dan Sistem Komputer',
        skills: ['Computer Networks', 'Operating Systems', 'Cybersecurity'],
    },
    'ai-predictive-project': {
        program: 'Teknik Informatika',
        focus: 'Artificial Intelligence',
        skills: ['Machine Learning', 'Data Science', 'Computer Vision'],
    },
    'mini-computer-architecture-design': {
        program: 'Sistem Komputer',
        focus: 'Arsitektur dan Organisasi Komputer',
        skills: [
            'Computer Architecture',
            'Digital Logic',
            'Microprocessor and Microcontroller',
        ],
    },
    'smart-iot-system': {
        program: 'Sistem Komputer',
        focus: 'Embedded System dan Internet of Things',
        skills: [
            'Embedded Systems',
            'Internet of Things',
            'Sensor and Actuator Integration',
        ],
    },
    'secure-network-design': {
        program: 'Sistem Komputer',
        focus: 'Jaringan dan Keamanan Komputer',
        skills: [
            'Computer Networks',
            'Network Administration',
            'Network Security',
        ],
    },
    'employee-organizational-assessment': {
        program: 'Psikologi',
        focus: 'Psikologi Industri dan Organisasi',
        skills: [
            'Employee Behavior',
            'Organizational Development',
            'Psychological Assessment',
        ],
    },
    'counseling-case-simulation': {
        program: 'Psikologi',
        focus: 'Konseling',
        skills: [
            'Counseling Skills',
            'Interpersonal Communication',
            'Emotional Intelligence',
        ],
    },
    'mini-psychological-research': {
        program: 'Psikologi',
        focus: 'Penelitian Psikologi',
        skills: [
            'Research Methodology',
            'Interview dan Observation',
            'Survey dan Data Analysis',
        ],
    },
    'crisis-communication-simulation': {
        program: 'Ilmu Komunikasi',
        focus: 'Public Relations',
        skills: [
            'Media Relations',
            'Corporate Communication',
            'Crisis Communication',
        ],
    },
    'news-reporting-project': {
        program: 'Ilmu Komunikasi',
        focus: 'Jurnalistik',
        skills: ['News Writing', 'Journalistic Interview', 'News Reporting'],
    },
    'digital-content-campaign': {
        program: 'Ilmu Komunikasi',
        focus: 'Digital Media',
        skills: [
            'Content Creation',
            'Social Media Management',
            'Video Production',
        ],
    },
};

export function getProjectCatalogEntry(
    slug: string,
    fallbackSkills: string[] = [],
): ProjectCatalogEntry {
    return (
        projectCatalog[slug] ?? {
            program: 'Jurusan pilihanmu',
            focus: 'Proyek Tugas Akhir',
            skills: fallbackSkills.slice(0, 3),
        }
    );
}
