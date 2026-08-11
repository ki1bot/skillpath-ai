export type User = {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    role: 'student' | 'admin';
    study_program?: string | null;
    semester?: number | null;
    interest_area?: string | null;
    experience?: string | null;
    weekly_study_hours?: number;
    target_career_id?: number | null;
    target_career?: {
        id: number;
        name: string;
        slug: string;
    } | null;
    onboarding_completed_at?: string | null;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Auth = {
    user: User | null;
};

export type TwoFactorSetupData = {
    svg: string;
    url: string;
};

export type TwoFactorSecretKey = {
    secretKey: string;
};
