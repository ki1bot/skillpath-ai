import { Form } from '@inertiajs/react';
import { Plus, Save } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
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

export function QuestionForm({
    question,
    assessments,
    skills,
    defaultAssessmentId,
}: Props) {
    const action = question
        ? `/admin/questions/${question.id}`
        : '/admin/questions';

    const [questionType, setQuestionType] = useState<
        'multiple_choice' | 'case' | 'practical'
    >(question?.question_type ?? 'multiple_choice');

    return (
        <Form
            action={action}
            method={question ? 'put' : 'post'}
            className="grid gap-5"
        >
            {({ processing }) => (
                <>
                    <div className="grid gap-4 md:grid-cols-4">
                        <SelectField
                            label="Assesment"
                            name="assessment_id"
                            defaultValue={
                                question?.assessment_id ??
                                defaultAssessmentId ??
                                ''
                            }
                            required
                        >
                            <option value="">Pilih Assesment</option>

                            {assessments.map((assessment) => (
                                <option
                                    key={assessment.id}
                                    value={assessment.id}
                                >
                                    {assessment.title}
                                </option>
                            ))}
                        </SelectField>

                        <SelectField
                            label="Keahlian"
                            name="skill_id"
                            defaultValue={question?.skill_id ?? ''}
                            required
                        >
                            <option value="">Pilih keahlian</option>

                            {skills.map((skill) => (
                                <option key={skill.id} value={skill.id}>
                                    {skill.name}
                                </option>
                            ))}
                        </SelectField>

                        <SelectField
                            label="Jenis soal"
                            name="question_type"
                            value={questionType}
                            onChange={(event) =>
                                setQuestionType(
                                    event.target.value as
                                        | 'multiple_choice'
                                        | 'case'
                                        | 'practical',
                                )
                            }
                            required
                        >
                            <option value="multiple_choice">
                                Pilihan ganda
                            </option>
                            <option value="case">Studi kasus</option>
                            <option value="practical">Tugas praktik</option>
                        </SelectField>

                        <InputField
                            label="Tingkat kesulitan"
                            name="difficulty"
                            defaultValue={question?.difficulty ?? 'Dasar'}
                            required
                        />
                    </div>

                    <TextareaField
                        label={
                            questionType === 'case'
                                ? 'Skenario kasus'
                                : 'Pertanyaan'
                        }
                        name="prompt"
                        rows={4}
                        defaultValue={question?.prompt}
                        required
                    />

                    {questionType === 'practical' && (
                        <div className="grid gap-4">
                            <TextareaField
                                label="Instruksi tugas praktik"
                                name="practical_instructions"
                                rows={5}
                                defaultValue={
                                    question?.practical_instructions ?? ''
                                }
                                required
                            />

                            <label className="flex items-center gap-3 rounded-[12px] border-2 border-foreground bg-muted p-4 text-sm font-black">
                                <input
                                    type="hidden"
                                    name="evidence_required"
                                    value="0"
                                />
                                <input
                                    type="checkbox"
                                    name="evidence_required"
                                    value="1"
                                    defaultChecked={
                                        question?.evidence_required ?? true
                                    }
                                    className="size-4 accent-black"
                                />
                                Wajib melampirkan tautan bukti praktik
                            </label>
                        </div>
                    )}

                    {questionType !== 'practical' && (
                        <input
                            type="hidden"
                            name="evidence_required"
                            value="0"
                        />
                    )}

                    <div className="grid gap-4 sm:grid-cols-2">
                        {(['A', 'B', 'C', 'D'] as const).map((letter) => (
                            <InputField
                                key={letter}
                                label={`Pilihan ${letter}`}
                                name="options[]"
                                defaultValue={question?.options?.[letter]}
                                required
                            />
                        ))}
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        <SelectField
                            label="Jawaban benar"
                            name="correct_answer"
                            defaultValue={question?.correct_answer ?? 'A'}
                        >
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </SelectField>

                        <InputField
                            label="Penjelasan jawaban"
                            name="explanation"
                            defaultValue={question?.explanation ?? ''}
                        />
                    </div>

                    <Button disabled={processing} className="w-full sm:w-fit">
                        {question ? (
                            <Save className="size-4" />
                        ) : (
                            <Plus className="size-4" />
                        )}

                        {question ? 'Simpan soal' : 'Tambah soal'}
                    </Button>
                </>
            )}
        </Form>
    );
}
