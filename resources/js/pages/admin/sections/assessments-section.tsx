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
            description="Kelola 50 soal pilihan ganda untuk Assessment awal setiap jurusan, serta Assessment tambahan jika diperlukan."
            accentClass="bg-[var(--neo-orange)] text-[#171717]"
        >
            <div className="rounded-xl border-2 border-foreground/15 bg-muted/20 p-4">
                <p className="text-sm font-black">Alur penilaian mahasiswa</p>

                <p className="mt-2 text-sm leading-6 text-muted-foreground">
                    Mahasiswa mengerjakan Assessment sebelum memulai roadmap.
                    Hasil Assessment digunakan untuk melihat kemampuan awal dan
                    menentukan prioritas belajar. Setelah itu, mahasiswa
                    menyelesaikan tugas praktik pada tahap Amatir, Menengah, dan
                    Ahli.
                </p>

                <p className="mt-3 text-sm leading-6 text-muted-foreground">
                    Soal Assessment dinilai secara otomatis. Tugas praktik
                    roadmap dan proyek diperiksa oleh admin melalui hasil yang
                    dikumpulkan mahasiswa.
                </p>
            </div>

            <AdminDetails
                title="Tambah Assessment tambahan"
                meta="Assessment tambahan tidak menggantikan Assessment awal wajib mahasiswa."
            >
                <AssessmentForm careers={careers} />
            </AdminDetails>

            <div className="grid gap-4">
                {assessments.map((assessment) => {
                    const isAcademic = Boolean(
                        assessment.study_program &&
                        assessment.study_program === assessment.career?.name &&
                        getStudyProgramDefinition(assessment.study_program),
                    );

                    return (
                        <AdminDetails
                            key={assessment.id}
                            title={assessment.title}
                            meta={`${
                                assessment.career?.name ??
                                'Jurusan belum tersedia'
                            } · ${assessment.questions.length} soal · ${
                                isAcademic
                                    ? 'Assessment wajib'
                                    : 'Assessment tambahan'
                            } · ${assessment.is_active ? 'Aktif' : 'Nonaktif'}`}
                        >
                            <div className="grid min-w-0 gap-6">
                                <AssessmentForm
                                    assessment={assessment}
                                    careers={careers}
                                />

                                {isAcademic ? (
                                    <div className="rounded-xl border-2 border-foreground/15 bg-muted/20 p-4">
                                        <p className="text-sm font-black">
                                            Assessment wajib jurusan
                                        </p>

                                        <p className="mt-2 text-sm leading-6 text-muted-foreground">
                                            Assessment ini menggunakan 50 soal
                                            yang mencakup sembilan kemampuan
                                            jurusan. Isi soal dapat diperbarui,
                                            tetapi jumlah dan hubungan
                                            kemampuannya tetap dijaga.
                                        </p>

                                        {assessment.questions.length !== 50 && (
                                            <p className="mt-3 text-sm font-bold text-destructive">
                                                Jumlah soal saat ini belum 50.
                                                Periksa kelengkapan bank soal
                                                sebelum digunakan.
                                            </p>
                                        )}
                                    </div>
                                ) : (
                                    <AdminDetails
                                        title="Tambah soal"
                                        meta="Tambahkan pertanyaan pilihan ganda untuk Assessment ini."
                                        subtle
                                    >
                                        <QuestionForm
                                            assessments={assessments}
                                            skills={skills}
                                            defaultAssessmentId={assessment.id}
                                        />
                                    </AdminDetails>
                                )}

                                <div className="grid min-w-0 gap-3">
                                    {assessment.questions.map(
                                        (question, index) => (
                                            <AdminDetails
                                                key={question.id}
                                                title={`Soal ${index + 1}: ${
                                                    question.prompt
                                                }`}
                                                meta={
                                                    question.skill?.name ??
                                                    'Kemampuan belum tersedia'
                                                }
                                                subtle
                                            >
                                                <div className="grid min-w-0 gap-5">
                                                    <QuestionForm
                                                        question={question}
                                                        assessments={
                                                            assessments
                                                        }
                                                        skills={skills}
                                                    />

                                                    {!isAcademic && (
                                                        <DeleteButton
                                                            action={`/admin/questions/${question.id}`}
                                                        />
                                                    )}
                                                </div>
                                            </AdminDetails>
                                        ),
                                    )}
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
