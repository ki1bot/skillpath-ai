import { AdminDetails } from '../components/admin-details';
import { AdminPanel } from '../components/admin-panel';
import { DeleteButton } from '../components/delete-button';
import { AssessmentForm } from '../forms/assessment-form';
import { QuestionForm } from '../forms/question-form';
import type { Assessment, Career, Skill } from '../types';

type Props = {
    assessments: Assessment[];
    careers: Career[];
    skills: Skill[];
};

export function AssessmentsSection({ assessments, careers, skills }: Props) {
    return (
        <AdminPanel
            title="Penilaian & soal"
            description="Kelola instrumen penilaian yang menjadi sumber utama skor kemampuan awal mahasiswa."
            accentClass="bg-[var(--neo-orange)] text-[#171717]"
        >
            <AdminDetails title="Tambah tugas baru">
                <AssessmentForm careers={careers} />
            </AdminDetails>

            <div className="grid gap-4">
                {assessments.map((assessment) => (
                    <AdminDetails
                        key={assessment.id}
                        title={assessment.title}
                        meta={`${assessment.questions.length} soal`}
                    >
                        <div className="grid gap-7">
                            <AssessmentForm
                                assessment={assessment}
                                careers={careers}
                            />

                            <AdminDetails title="Tambah soal" subtle>
                                <QuestionForm
                                    assessments={assessments}
                                    skills={skills}
                                    defaultAssessmentId={assessment.id}
                                />
                            </AdminDetails>

                            <div className="grid gap-3">
                                {assessment.questions.map((question) => (
                                    <AdminDetails
                                        key={question.id}
                                        title={
                                            question.skill?.name
                                                ? `${question.skill.name}: ${question.prompt}`
                                                : question.prompt
                                        }
                                        subtle
                                    >
                                        <div className="grid gap-5">
                                            <QuestionForm
                                                question={question}
                                                assessments={assessments}
                                                skills={skills}
                                            />

                                            <DeleteButton
                                                action={`/admin/questions/${question.id}`}
                                            />
                                        </div>
                                    </AdminDetails>
                                ))}
                            </div>

                            <div className="border-t-2 border-foreground/15 pt-5">
                                <DeleteButton
                                    action={`/admin/assessments/${assessment.id}`}
                                />
                            </div>
                        </div>
                    </AdminDetails>
                ))}
            </div>
        </AdminPanel>
    );
}
