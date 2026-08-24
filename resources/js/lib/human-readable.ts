export function humanizeRoadmapReason(value: string): string {
    return value
        .replace(/\bAssesment\b/g, 'Assesment')
        .replace(/\bassesment\b/g, 'assesment')
        .replace(/\bAssessment\b/g, 'Assesment')
        .replace(/\bassessment\b/g, 'assesment')
        .replace(/\bRoadmap\b/g, 'Jalur belajar')
        .replace(/\broadmap\b/g, 'jalur belajar')
        .replace(/\bSkill\b/g, 'Kemampuan')
        .replace(/\bskill\b/g, 'kemampuan')
        .replace(/\bSkor\b/g, 'Nilai')
        .replace(/\bskor\b/g, 'nilai');
}

export function humanizeStageTitle(value: string): string {
    if (value === 'Quality & Delivery') {
        return 'Penyelesaian dan hasil akhir';
    }

    return value;
}
