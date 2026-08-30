<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class AcademicAssessmentSkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            ['si-sql-data-processing', 'SQL & data processing', 'Analisis Data', 'Memahami konsep dan penerapan SQL & data processing dalam konteks Analisis Data.', 'Menengah'],
            ['si-spreadsheet-data-analysis', 'Spreadsheet analysis', 'Analisis Data', 'Memahami konsep dan penerapan Spreadsheet analysis dalam konteks Analisis Data.', 'Dasar'],
            ['si-business-intelligence-data-visualization', 'Business Intelligence', 'Analisis Data', 'Memahami konsep dan penerapan Business Intelligence dalam konteks Analisis Data.', 'Menengah'],
            ['si-data-visualization', 'Data visualization', 'Analisis Data', 'Memahami konsep dan penerapan Data visualization dalam konteks Analisis Data.', 'Menengah'],
            ['si-scenario-based-data-analysis', 'Scenario-based data analysis', 'Analisis Data', 'Memahami konsep dan penerapan Scenario-based data analysis dalam konteks Analisis Data.', 'Menengah'],
            ['si-database-management', 'Database', 'Pengembangan Sistem', 'Memahami konsep dan penerapan Database dalam konteks Pengembangan Sistem.', 'Menengah'],
            ['si-web-development', 'Web development', 'Pengembangan Sistem', 'Memahami konsep dan penerapan Web development dalam konteks Pengembangan Sistem.', 'Menengah'],
            ['si-system-analysis-design', 'System analysis', 'Pengembangan Sistem', 'Memahami konsep dan penerapan System analysis dalam konteks Pengembangan Sistem.', 'Menengah'],
            ['si-erd-uml', 'ERD/UML', 'Pengembangan Sistem', 'Memahami konsep dan penerapan ERD/UML dalam konteks Pengembangan Sistem.', 'Menengah'],
            ['si-problem-solving', 'Problem solving', 'Pengembangan Sistem', 'Memahami konsep dan penerapan Problem solving dalam konteks Pengembangan Sistem.', 'Menengah'],
            ['si-ui-design', 'UI design', 'UI/UX', 'Memahami konsep dan penerapan UI design dalam konteks UI/UX.', 'Dasar'],
            ['si-wireframing-prototyping', 'Wireframing', 'UI/UX', 'Memahami konsep dan penerapan Wireframing dalam konteks UI/UX.', 'Dasar'],
            ['si-prototyping', 'Prototyping', 'UI/UX', 'Memahami konsep dan penerapan Prototyping dalam konteks UI/UX.', 'Dasar'],
            ['si-user-research', 'User research', 'UI/UX', 'Memahami konsep dan penerapan User research dalam konteks UI/UX.', 'Menengah'],
            ['si-usability', 'Usability', 'UI/UX', 'Memahami konsep dan penerapan Usability dalam konteks UI/UX.', 'Menengah'],

            ['man-branding', 'Branding', 'Marketing', 'Memahami konsep dan penerapan Branding dalam konteks Marketing.', 'Menengah'],
            ['man-digital-marketing', 'Digital marketing', 'Marketing', 'Memahami konsep dan penerapan Digital marketing dalam konteks Marketing.', 'Menengah'],
            ['man-market-research', 'Market research', 'Marketing', 'Memahami konsep dan penerapan Market research dalam konteks Marketing.', 'Menengah'],
            ['man-marketing-strategy', 'Marketing strategy', 'Marketing', 'Memahami konsep dan penerapan Marketing strategy dalam konteks Marketing.', 'Menengah'],
            ['man-campaign-analysis', 'Campaign analysis', 'Marketing', 'Memahami konsep dan penerapan Campaign analysis dalam konteks Marketing.', 'Menengah'],
            ['man-financial-planning', 'Financial planning', 'Keuangan', 'Memahami konsep dan penerapan Financial planning dalam konteks Keuangan.', 'Menengah'],
            ['man-financial-analysis', 'Financial analysis', 'Keuangan', 'Memahami konsep dan penerapan Financial analysis dalam konteks Keuangan.', 'Menengah'],
            ['man-financial-ratios', 'Financial ratios', 'Keuangan', 'Memahami konsep dan penerapan Financial ratios dalam konteks Keuangan.', 'Menengah'],
            ['man-investment-management', 'Investment basics', 'Keuangan', 'Memahami konsep dan penerapan Investment basics dalam konteks Keuangan.', 'Dasar'],
            ['man-financial-decision-making', 'Financial decision making', 'Keuangan', 'Memahami konsep dan penerapan Financial decision making dalam konteks Keuangan.', 'Menengah'],
            ['man-recruitment-selection', 'Recruitment', 'Human Resources', 'Memahami konsep dan penerapan Recruitment dalam konteks Human Resources.', 'Menengah'],
            ['man-candidate-selection', 'Candidate selection', 'Human Resources', 'Memahami konsep dan penerapan Candidate selection dalam konteks Human Resources.', 'Menengah'],
            ['man-interview', 'Interview', 'Human Resources', 'Memahami konsep dan penerapan Interview dalam konteks Human Resources.', 'Menengah'],
            ['man-performance-management', 'Performance management', 'Human Resources', 'Memahami konsep dan penerapan Performance management dalam konteks Human Resources.', 'Menengah'],
            ['man-talent-management', 'Talent management', 'Human Resources', 'Memahami konsep dan penerapan Talent management dalam konteks Human Resources.', 'Menengah'],

            ['ti-algorithms-data-structures', 'Algoritma', 'Pemrograman & Rekayasa Perangkat Lunak', 'Memahami konsep dan penerapan Algoritma dalam konteks Pemrograman & Rekayasa Perangkat Lunak.', 'Menengah'],
            ['ti-data-structures', 'Data structure', 'Pemrograman & Rekayasa Perangkat Lunak', 'Memahami konsep dan penerapan Data structure dalam konteks Pemrograman & Rekayasa Perangkat Lunak.', 'Menengah'],
            ['ti-object-oriented-programming', 'OOP', 'Pemrograman & Rekayasa Perangkat Lunak', 'Memahami konsep dan penerapan OOP dalam konteks Pemrograman & Rekayasa Perangkat Lunak.', 'Menengah'],
            ['ti-software-engineering', 'Software engineering', 'Pemrograman & Rekayasa Perangkat Lunak', 'Memahami konsep dan penerapan Software engineering dalam konteks Pemrograman & Rekayasa Perangkat Lunak.', 'Menengah'],
            ['ti-debugging', 'Debugging', 'Pemrograman & Rekayasa Perangkat Lunak', 'Memahami konsep dan penerapan Debugging dalam konteks Pemrograman & Rekayasa Perangkat Lunak.', 'Menengah'],
            ['ti-computer-networks', 'Computer networks', 'Jaringan & Sistem Komputer', 'Memahami konsep dan penerapan Computer networks dalam konteks Jaringan & Sistem Komputer.', 'Menengah'],
            ['ti-operating-systems', 'Operating systems', 'Jaringan & Sistem Komputer', 'Memahami konsep dan penerapan Operating systems dalam konteks Jaringan & Sistem Komputer.', 'Menengah'],
            ['ti-network-troubleshooting', 'Network troubleshooting', 'Jaringan & Sistem Komputer', 'Memahami konsep dan penerapan Network troubleshooting dalam konteks Jaringan & Sistem Komputer.', 'Menengah'],
            ['ti-cybersecurity', 'Cybersecurity', 'Jaringan & Sistem Komputer', 'Memahami konsep dan penerapan Cybersecurity dalam konteks Jaringan & Sistem Komputer.', 'Menengah'],
            ['ti-system-administration', 'System administration', 'Jaringan & Sistem Komputer', 'Memahami konsep dan penerapan System administration dalam konteks Jaringan & Sistem Komputer.', 'Menengah'],
            ['ti-machine-learning', 'Machine learning', 'Artificial Intelligence', 'Memahami konsep dan penerapan Machine learning dalam konteks Artificial Intelligence.', 'Menengah'],
            ['ti-data-science', 'Data science', 'Artificial Intelligence', 'Memahami konsep dan penerapan Data science dalam konteks Artificial Intelligence.', 'Menengah'],
            ['ti-statistics', 'Statistics', 'Artificial Intelligence', 'Memahami konsep dan penerapan Statistics dalam konteks Artificial Intelligence.', 'Menengah'],
            ['ti-model-evaluation', 'Model evaluation', 'Artificial Intelligence', 'Memahami konsep dan penerapan Model evaluation dalam konteks Artificial Intelligence.', 'Menengah'],
            ['ti-computer-vision', 'Computer vision', 'Artificial Intelligence', 'Memahami konsep dan penerapan Computer vision dalam konteks Artificial Intelligence.', 'Menengah'],

            ['sk-computer-architecture', 'Computer architecture', 'Arsitektur & Organisasi Komputer', 'Memahami konsep dan penerapan Computer architecture dalam konteks Arsitektur & Organisasi Komputer.', 'Menengah'],
            ['sk-digital-logic', 'Digital logic', 'Arsitektur & Organisasi Komputer', 'Memahami konsep dan penerapan Digital logic dalam konteks Arsitektur & Organisasi Komputer.', 'Dasar'],
            ['sk-processor', 'Processor', 'Arsitektur & Organisasi Komputer', 'Memahami konsep dan penerapan Processor dalam konteks Arsitektur & Organisasi Komputer.', 'Menengah'],
            ['sk-memory', 'Memory', 'Arsitektur & Organisasi Komputer', 'Memahami konsep dan penerapan Memory dalam konteks Arsitektur & Organisasi Komputer.', 'Menengah'],
            ['sk-microprocessor-microcontroller', 'Microprocessor', 'Arsitektur & Organisasi Komputer', 'Memahami konsep dan penerapan Microprocessor dalam konteks Arsitektur & Organisasi Komputer.', 'Menengah'],
            ['sk-microcontroller', 'Microcontroller', 'Embedded System & IoT', 'Memahami konsep dan penerapan Microcontroller dalam konteks Embedded System & IoT.', 'Menengah'],
            ['sk-embedded-systems', 'Embedded system', 'Embedded System & IoT', 'Memahami konsep dan penerapan Embedded system dalam konteks Embedded System & IoT.', 'Menengah'],
            ['sk-internet-of-things', 'IoT', 'Embedded System & IoT', 'Memahami konsep dan penerapan IoT dalam konteks Embedded System & IoT.', 'Menengah'],
            ['sk-sensor-actuator-integration', 'Sensor', 'Embedded System & IoT', 'Memahami konsep dan penerapan Sensor dalam konteks Embedded System & IoT.', 'Menengah'],
            ['sk-actuator', 'Actuator', 'Embedded System & IoT', 'Memahami konsep dan penerapan Actuator dalam konteks Embedded System & IoT.', 'Menengah'],
            ['sk-computer-networks', 'Networking', 'Jaringan & Keamanan Komputer', 'Memahami konsep dan penerapan Networking dalam konteks Jaringan & Keamanan Komputer.', 'Menengah'],
            ['sk-network-administration', 'Network administration', 'Jaringan & Keamanan Komputer', 'Memahami konsep dan penerapan Network administration dalam konteks Jaringan & Keamanan Komputer.', 'Menengah'],
            ['sk-network-security', 'Network security', 'Jaringan & Keamanan Komputer', 'Memahami konsep dan penerapan Network security dalam konteks Jaringan & Keamanan Komputer.', 'Menengah'],
            ['sk-firewall', 'Firewall', 'Jaringan & Keamanan Komputer', 'Memahami konsep dan penerapan Firewall dalam konteks Jaringan & Keamanan Komputer.', 'Menengah'],
            ['sk-threat-detection', 'Threat detection', 'Jaringan & Keamanan Komputer', 'Memahami konsep dan penerapan Threat detection dalam konteks Jaringan & Keamanan Komputer.', 'Menengah'],

            ['psi-employee-behavior', 'Employee behavior', 'Psikologi Industri & Organisasi', 'Memahami konsep dan penerapan Employee behavior dalam konteks Psikologi Industri & Organisasi.', 'Menengah'],
            ['psi-organizational-behavior', 'Organizational behavior', 'Psikologi Industri & Organisasi', 'Memahami konsep dan penerapan Organizational behavior dalam konteks Psikologi Industri & Organisasi.', 'Menengah'],
            ['psi-work-style-assessment', 'Work-style assessment', 'Psikologi Industri & Organisasi', 'Memahami konsep dan penerapan Work-style assessment dalam konteks Psikologi Industri & Organisasi.', 'Menengah'],
            ['psi-psychological-assessment', 'Psychological assessment', 'Psikologi Industri & Organisasi', 'Memahami konsep dan penerapan Psychological assessment dalam konteks Psikologi Industri & Organisasi.', 'Menengah'],
            ['psi-organizational-development', 'Organizational development', 'Psikologi Industri & Organisasi', 'Memahami konsep dan penerapan Organizational development dalam konteks Psikologi Industri & Organisasi.', 'Menengah'],
            ['psi-interpersonal-communication', 'Communication', 'Konseling', 'Memahami konsep dan penerapan Communication dalam konteks Konseling.', 'Dasar'],
            ['psi-counseling-skills', 'Active listening', 'Konseling', 'Memahami konsep dan penerapan Active listening dalam konteks Konseling.', 'Menengah'],
            ['psi-empathy', 'Empathy', 'Konseling', 'Memahami konsep dan penerapan Empathy dalam konteks Konseling.', 'Menengah'],
            ['psi-emotional-intelligence', 'Emotional intelligence', 'Konseling', 'Memahami konsep dan penerapan Emotional intelligence dalam konteks Konseling.', 'Menengah'],
            ['psi-counseling-scenario', 'Counseling scenario', 'Konseling', 'Memahami konsep dan penerapan Counseling scenario dalam konteks Konseling.', 'Menengah'],
            ['psi-research-methodology', 'Research methodology', 'Penelitian Psikologi', 'Memahami konsep dan penerapan Research methodology dalam konteks Penelitian Psikologi.', 'Menengah'],
            ['psi-interview-observation', 'Interview', 'Penelitian Psikologi', 'Memahami konsep dan penerapan Interview dalam konteks Penelitian Psikologi.', 'Menengah'],
            ['psi-observation', 'Observation', 'Penelitian Psikologi', 'Memahami konsep dan penerapan Observation dalam konteks Penelitian Psikologi.', 'Menengah'],
            ['psi-survey-data-analysis', 'Survey', 'Penelitian Psikologi', 'Memahami konsep dan penerapan Survey dalam konteks Penelitian Psikologi.', 'Menengah'],
            ['psi-data-analysis', 'Data analysis', 'Penelitian Psikologi', 'Memahami konsep dan penerapan Data analysis dalam konteks Penelitian Psikologi.', 'Menengah'],

            ['ikom-media-relations', 'Media relations', 'Public Relations', 'Memahami konsep dan penerapan Media relations dalam konteks Public Relations.', 'Menengah'],
            ['ikom-corporate-communication', 'Corporate communication', 'Public Relations', 'Memahami konsep dan penerapan Corporate communication dalam konteks Public Relations.', 'Menengah'],
            ['ikom-crisis-communication', 'Crisis communication', 'Public Relations', 'Memahami konsep dan penerapan Crisis communication dalam konteks Public Relations.', 'Menengah'],
            ['ikom-public-communication', 'Public communication', 'Public Relations', 'Memahami konsep dan penerapan Public communication dalam konteks Public Relations.', 'Menengah'],
            ['ikom-reputation-management', 'Reputation management', 'Public Relations', 'Memahami konsep dan penerapan Reputation management dalam konteks Public Relations.', 'Menengah'],
            ['ikom-news-writing', 'News writing', 'Jurnalistik', 'Memahami konsep dan penerapan News writing dalam konteks Jurnalistik.', 'Dasar'],
            ['ikom-journalistic-interview', 'Interview', 'Jurnalistik', 'Memahami konsep dan penerapan Interview dalam konteks Jurnalistik.', 'Menengah'],
            ['ikom-news-reporting', 'News reporting', 'Jurnalistik', 'Memahami konsep dan penerapan News reporting dalam konteks Jurnalistik.', 'Menengah'],
            ['ikom-fact-checking', 'Fact checking', 'Jurnalistik', 'Memahami konsep dan penerapan Fact checking dalam konteks Jurnalistik.', 'Menengah'],
            ['ikom-journalistic-ethics', 'Journalistic ethics', 'Jurnalistik', 'Memahami konsep dan penerapan Journalistic ethics dalam konteks Jurnalistik.', 'Menengah'],
            ['ikom-content-creation', 'Content creation', 'Digital Media', 'Memahami konsep dan penerapan Content creation dalam konteks Digital Media.', 'Menengah'],
            ['ikom-social-media-management', 'Social media', 'Digital Media', 'Memahami konsep dan penerapan Social media dalam konteks Digital Media.', 'Menengah'],
            ['ikom-video-production', 'Video production', 'Digital Media', 'Memahami konsep dan penerapan Video production dalam konteks Digital Media.', 'Menengah'],
            ['ikom-content-strategy', 'Content strategy', 'Digital Media', 'Memahami konsep dan penerapan Content strategy dalam konteks Digital Media.', 'Menengah'],
            ['ikom-audience-analysis', 'Audience analysis', 'Digital Media', 'Memahami konsep dan penerapan Audience analysis dalam konteks Digital Media.', 'Menengah'],
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
