export type Skill = {
    id: number;
    name: string;
    slug: string;
    category: string;
    description: string;
    difficulty: string;
    prerequisites?: Skill[];
    pivot?: {
        target_level?: number;
        importance_weight?: string | number;
        is_required?: boolean;
        required_level?: number;
        weight?: string | number;
    };
};

export type Career = {
    id: number;
    name: string;
    slug: string;
    tagline: string;
    description: string;
    responsibilities: string[];
    difficulty: string;
    accent: string;
    is_active: boolean;
    skills: Skill[];
};

export type Question = {
    id: number;
    assessment_id: number;
    skill_id: number;
    question_type: 'multiple_choice' | 'case' | 'practical';
    prompt: string;
    practical_instructions?: string | null;
    evidence_required: boolean;
    options: Record<'A' | 'B' | 'C' | 'D', string>;
    correct_answer: 'A' | 'B' | 'C' | 'D';
    explanation?: string | null;
    difficulty: string;
    skill?: Skill | null;
};

export type Assessment = {
    id: number;
    career_id: number;
    title: string;
    description: string;
    duration_minutes: number;
    is_active: boolean;
    career?: Career | null;
    questions: Question[];
};

export type Material = {
    id: number;
    skill_id: number;
    material_type: 'core' | 'reinforcement';
    reinforcement_for_material_id?: number | null;
    is_active: boolean;
    title: string;
    slug: string;
    summary: string;
    learning_objectives: string[];
    difficulty: string;
    estimated_minutes: number;
    resource_title?: string | null;
    resource_url?: string | null;
    practice_task: string;
    quiz_question: string;
    quiz_options: Record<'A' | 'B' | 'C' | 'D', string>;
    quiz_answer: 'A' | 'B' | 'C' | 'D';
    quiz_explanation?: string | null;
    skill?: Skill | null;
};

export type Project = {
    id: number;
    career_id: number;
    title: string;
    slug: string;
    summary: string;
    problem_statement: string;
    difficulty: string;
    minimum_features: string[];
    stretch_features?: string[] | null;
    completion_criteria: string[];
    estimated_hours: number;
    career?: Career | null;
    skills: Skill[];
};

export type Prerequisite = {
    id: number;
    factor: string | number;
    skill_name: string;
    prerequisite_name: string;
};

export type AdminStats = {
    users: number;
    careers: number;
    skills: number;
    materials: number;
    projects: number;
    assessmentAttempts: number;
};

export type AdminPageProps = {
    stats: AdminStats;
    careers: Career[];
    skills: Skill[];
    prerequisites: Prerequisite[];
    assessments: Assessment[];
    materials: Material[];
    projects: Project[];
};
