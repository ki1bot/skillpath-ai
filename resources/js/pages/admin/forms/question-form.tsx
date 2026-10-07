import { Form } from '@inertiajs/react';
import { Plus, Save } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { getStudyProgramDefinition } from '@/lib/academic-programs';
import {
    InputField,
    SelectField,
    TextareaField,
} from '../components/form-controls';
import type { Assessment, Question, Skill } from '../types';

type Props = {
    question?: Question;
    assessments: Assessment[];
    skills: Skill[];
    defaultAssessmentId?: number;
};

const stages = ['Amatir', 'Menengah', 'Ahli'] as const;

function normalizeDifficulty(value?: string): string {
    if (value === 'Dasar') {
        return 'Amatir';
    }

    if (value === 'Lanjutan') {
        return 'Ahli';
    }

    if (stages.some((stage) => stage === value)) {
        return value ?? 'Menengah';
    }

    return 'Menengah';
}

export function QuestionForm({
    question,
    assessments,
    skills,
    defaultAssessmentId,
}: Props) {
    const action = question
        ? `/admin/questions/${question.id}`
        : '/admin/questions';

    const [assessmentId, setAssessmentId] = useState<number | ''>(
        question?.assessment_id ?? defaultAssessmentId ?? '',
    );

    const [skillId, setSkillId] = useState<number | ''>(
        question?.skill_id ?? '',
    );

    const assessment = assessments.find((item) => item.id === assessmentId);

    const program = getStudyProgramDefinition(assessment?.career?.name ?? '');

    const allowedSkills = program
        ? new Set(program.areas.flatMap((area) => area.skills))
        : null;

    const selectableSkills = allowedSkills
        ? skills.filter((skill) => allowedSkills.has(skill.name))
        : skills;

    const isAcademicQuestion = Boolean(question && program);

    const handleAssessmentChange = (value: string) => {
        setAssessmentId(value ? Number(value) : '');
        setSkillId('');
    };

    return (
        <Form
            action={action}
            method={question ? 'put' : 'post'}
            className="grid min-w-0 gap-5"
        >
            {({ processing, errors }) => (
                <>
                    <input
                        type="hidden"
                        name="question_type"
                        value="multiple_choice"
                    />

                    <input type="hidden" name="evidence_required" value="0" />

                    <div className="rounded-xl border-2 border-foreground/15 bg-muted/20 p-4">
                        <p className="text-sm font-black">Soal pilihan ganda</p>

                        <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                            Assessment mengukur kemampuan awal mahasiswa. Tahap
                            Amatir, Menengah, dan Ahli pada formulir ini
                            menunjukkan kompleksitas soal, bukan tahap yang
                            terbuka di roadmap.
                        </p>
                    </div>

                    <div className="grid min-w-0 gap-4 md:grid-cols-2">
                        <div className="min-w-0">
                            <SelectField
                                label="Assessment"
                                name={
                                    isAcademicQuestion
                                        ? undefined
                                        : 'assessment_id'
                                }
                                value={assessmentId}
                                disabled={isAcademicQuestion}
                                onChange={(event) =>
                                    handleAssessmentChange(event.target.value)
                                }
                                required
                            >
                                <option value="">Pilih Assessment</option>

                                {assessments.map((item) => (
                                    <option key={item.id} value={item.id}>
                                        {item.title}
                                    </option>
                                ))}
                            </SelectField>

                            {isAcademicQuestion && (
                                <input
                                    type="hidden"
                                    name="assessment_id"
                                    value={assessmentId}
                                />
                            )}
                        </div>

                        <div className="min-w-0">
                            <SelectField
                                label="Kemampuan yang diuji"
                                name={
                                    isAcademicQuestion ? undefined : 'skill_id'
                                }
                                value={skillId}
                                disabled={isAcademicQuestion}
                                onChange={(event) =>
                                    setSkillId(
                                        event.target.value
                                            ? Number(event.target.value)
                                            : '',
                                    )
                                }
                                required
                            >
                                <option value="">Pilih kemampuan</option>

                                {selectableSkills.map((skill) => (
                                    <option key={skill.id} value={skill.id}>
                                        {skill.name}
                                    </option>
                                ))}
                            </SelectField>

                            {isAcademicQuestion && (
                                <input
                                    type="hidden"
                                    name="skill_id"
                                    value={skillId}
                                />
                            )}
                        </div>

                        <SelectField
                            label="Kompleksitas soal"
                            name="difficulty"
                            defaultValue={normalizeDifficulty(
                                question?.difficulty,
                            )}
                            required
                        >
                            {stages.map((stage) => (
                                <option key={stage} value={stage}>
                                    {stage}
                                </option>
                            ))}
                        </SelectField>

                        <div className="flex items-end">
                            <p className="rounded-lg border-2 border-foreground/15 bg-muted/20 px-4 py-3 text-xs leading-5 font-semibold text-muted-foreground">
                                Jenis evaluasi: pilihan ganda
                            </p>
                        </div>
                    </div>

                    <TextareaField
                        label="Pertanyaan"
                        name="prompt"
                        rows={4}
                        maxLength={2000}
                        defaultValue={question?.prompt ?? ''}
                        required
                    />

                    <div className="grid min-w-0 gap-4 md:grid-cols-2">
                        {(['A', 'B', 'C', 'D'] as const).map((letter) => (
                            <InputField
                                key={letter}
                                label={`Pilihan ${letter}`}
                                name="options[]"
                                defaultValue={question?.options?.[letter] ?? ''}
                                maxLength={500}
                                required
                            />
                        ))}
                    </div>

                    <div className="grid min-w-0 gap-4 md:grid-cols-2">
                        <SelectField
                            label="Jawaban benar"
                            name="correct_answer"
                            defaultValue={question?.correct_answer ?? 'A'}
                            required
                        >
                            {(['A', 'B', 'C', 'D'] as const).map((letter) => (
                                <option key={letter} value={letter}>
                                    Pilihan {letter}
                                </option>
                            ))}
                        </SelectField>

                        <InputField
                            label="Penjelasan jawaban"
                            name="explanation"
                            defaultValue={question?.explanation ?? ''}
                            maxLength={2000}
                        />
                    </div>

                    {Object.keys(errors).length > 0 && (
                        <div className="rounded-xl border-2 border-destructive/30 p-4">
                            {Object.entries(errors).map(([field, message]) => (
                                <p
                                    key={field}
                                    className="text-xs leading-5 font-bold text-destructive"
                                >
                                    {message}
                                </p>
                            ))}
                        </div>
                    )}

                    <div>
                        <Button type="submit" disabled={processing}>
                            {question ? (
                                <Save className="size-4" />
                            ) : (
                                <Plus className="size-4" />
                            )}

                            {processing
                                ? 'Menyimpan...'
                                : question
                                  ? 'Simpan soal'
                                  : 'Tambah soal'}
                        </Button>
                    </div>
                </>
            )}
        </Form>
    );
}
