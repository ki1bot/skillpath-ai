import { Form } from '@inertiajs/react';
import { Plus, Save } from 'lucide-react';
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

    return (
        <Form
            action={action}
            method={question ? 'put' : 'post'}
            className="grid gap-5"
        >
            {({ processing }) => (
                <>
                    <div className="grid gap-4 md:grid-cols-3">
                        <SelectField
                            label="Asesmen"
                            name="assessment_id"
                            defaultValue={
                                question?.assessment_id ??
                                defaultAssessmentId ??
                                ''
                            }
                            required
                        >
                            <option value="">Pilih asesmen</option>

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
                            label="Skill"
                            name="skill_id"
                            defaultValue={question?.skill_id ?? ''}
                            required
                        >
                            <option value="">Pilih skill</option>

                            {skills.map((skill) => (
                                <option key={skill.id} value={skill.id}>
                                    {skill.name}
                                </option>
                            ))}
                        </SelectField>

                        <InputField
                            label="Tingkat kesulitan"
                            name="difficulty"
                            defaultValue={question?.difficulty ?? 'Dasar'}
                            required
                        />
                    </div>

                    <TextareaField
                        label="Pertanyaan"
                        name="prompt"
                        rows={4}
                        defaultValue={question?.prompt}
                        required
                    />

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
