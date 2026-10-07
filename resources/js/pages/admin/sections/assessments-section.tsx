import { getStudyProgramDefinition } from '@/lib/academic-programs';
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
            title="Assessment dan soal"
            description="Kelola pertanyaan untuk mengukur kemampuan awal mahasiswa. Setiap jurusan akademik mempunyai 50 soal pilihan ganda."
            accentClass="bg-[var(--neo-orange)] text-[#171717]"
        >
            <div className="rounded-xl border-2 border-foreground/15 bg-muted/20 p-4">
                <p className="text-sm font-black">
                    Perbedaan Assessment dan roadmap
                </p>

                <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                    Assessment dikerjakan di awal untuk memetakan kemampuan
                    mahasiswa. Setelah selesai, mahasiswa mengikuti roadmap yang
                    terdiri dari Amatir, Menengah, dan Ahli. Materi pada roadmap
                    dinilai melalui tugas praktik, bukan soal pilihan ganda.
                </p>
            </div>

            <AdminDetails title="Tambah Assessment tambahan">
                <AssessmentForm careers={careers} />
            </AdminDetails>

            <div className="grid gap-4">
                {assessments.map((assessment) => {
                    const isAcademic = Boolean(
                        getStudyProgramDefinition(
                            assessment.career?.name ?? '',
                        ),
                    );

                    return (
                        <AdminDetails
                            key={assessment.id}
                            title={assessment.title}
                            meta={`${assessment.questions.length} soal · ${
                                isAcademic
                                    ? 'Assessment akademik'
                                    : 'Assessment tambahan'
                            }`}
                        >
                            <div className="grid gap-6">
                                <AssessmentForm
                                    assessment={assessment}
                                    careers={careers}
                                />

                                {isAcademic && (
                                    <div className="rounded-xl border-2 border-foreground/15 bg-muted/20 p-4">
                                        <p className="text-sm font-black">
                                            Jumlah soal akademik tetap
                                        </p>

                                        <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                                            Assessment ini menggunakan 50 soal
                                            dari sembilan kemampuan jurusan.
                                            Anda dapat memperbarui isi soal,
                                            pilihan jawaban, dan penjelasannya,
                                            tetapi tidak dapat menambah,
                                            menghapus, atau memindahkan soal ke
                                            kemampuan lain.
                                        </p>

                                        {assessment.questions.length !== 50 && (
                                            <p className="mt-3 text-sm font-black text-destructive">
                                                Perhatian: jumlah soal sekarang
                                                bukan 50. Periksa data
                                                Assessment sebelum digunakan
                                                mahasiswa.
                                            </p>
                                        )}
                                    </div>
                                )}

                                {!isAcademic && (
                                    <AdminDetails title="Tambah soal" subtle>
                                        <QuestionForm
                                            assessments={assessments}
                                            skills={skills}
                                            defaultAssessmentId={assessment.id}
                                        />
                                    </AdminDetails>
                                )}

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

                                                {!isAcademic && (
                                                    <DeleteButton
                                                        action={`/admin/questions/${question.id}`}
                                                    />
                                                )}
                                            </div>
                                        </AdminDetails>
                                    ))}
                                </div>

                                {!isAcademic && (
                                    <div className="border-t-2 border-foreground/15 pt-5">
                                        <DeleteButton
                                            action={`/admin/assessments/${assessment.id}`}
                                        />
                                    </div>
                                )}
                            </div>
                        </AdminDetails>
                    );
                })}
            </div>
        </AdminPanel>
    );
}
