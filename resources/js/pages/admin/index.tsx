import { Form, Head } from '@inertiajs/react';
import {
    BookOpenCheck,
    BriefcaseBusiness,
    Database,
    Layers3,
    Plus,
    ShieldCheck,
    Trash2,
    UsersRound,
    Wrench,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type Skill = {
    id: number;
    name: string;
    slug: string;
    category: string;
    description: string;
    difficulty: string;
    prerequisites?: Array<{
        id: number;
        name: string;
    }>;
    pivot?: {
        target_level?: number;
        importance_weight?: number;
        is_required?: boolean;
        required_level?: number;
        weight?: number;
    };
};

type Career = {
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

type Question = {
    id: number;
    assessment_id: number;
    skill_id: number;
    prompt: string;
    options: Record<'A' | 'B' | 'C' | 'D', string>;
    correct_answer: string;
    explanation?: string | null;
    difficulty: string;
    skill?: Skill;
};

type Assessment = {
    id: number;
    career_id: number;
    title: string;
    description: string;
    duration_minutes: number;
    is_active: boolean;
    career?: Career;
    questions: Question[];
};

type Material = {
    id: number;
    skill_id: number;
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
    quiz_answer: string;
    quiz_explanation?: string | null;
    skill?: Skill;
};

type Project = {
    id: number;
    career_id: number;
    title: string;
    slug: string;
    summary: string;
    problem_statement: string;
    difficulty: string;
    minimum_features: string[];
    stretch_features: string[];
    completion_criteria: string[];
    estimated_hours: number;
    career?: Career;
    skills: Skill[];
};

type Prerequisite = {
    id: number;
    factor: number;
    skill_name: string;
    prerequisite_name: string;
};

const fieldClass = 'grid gap-2 text-sm font-black';

const selectClass =
    'h-10 w-full border-2 border-black bg-white px-3 text-sm font-bold outline-none focus:shadow-[3px_3px_0_#111]';

const textareaClass =
    'w-full resize-y border-2 border-black bg-white p-3 text-sm font-medium outline-none focus:shadow-[3px_3px_0_#111]';

function DeleteButton({
    action,
    label = 'Hapus',
}: {
    action: string;
    label?: string;
}) {
    return (
        <Form action={action} method="delete">
            {({ processing }) => (
                <Button
                    type="submit"
                    variant="destructive"
                    size="sm"
                    disabled={processing}
                >
                    <Trash2 className="size-4" />
                    {label}
                </Button>
            )}
        </Form>
    );
}

function CareerForm({ career }: { career?: Career }) {
    const action = career ? `/admin/careers/${career.slug}` : '/admin/careers';

    return (
        <Form
            action={action}
            method={career ? 'put' : 'post'}
            className="grid gap-4"
        >
            {({ processing, errors }) => (
                <>
                    <div className="grid gap-4 md:grid-cols-2">
                        <label className={fieldClass}>
                            Nama
                            <Input
                                name="name"
                                defaultValue={career?.name}
                                required
                            />
                        </label>

                        <label className={fieldClass}>
                            Tagline
                            <Input
                                name="tagline"
                                defaultValue={career?.tagline}
                                required
                            />
                        </label>

                        <label className={fieldClass}>
                            Kesulitan
                            <Input
                                name="difficulty"
                                defaultValue={career?.difficulty ?? 'Menengah'}
                                required
                            />
                        </label>

                        <label className={fieldClass}>
                            Aksen
                            <Input
                                name="accent"
                                defaultValue={career?.accent ?? '#C7FF5E'}
                                required
                            />
                        </label>
                    </div>

                    <label className={fieldClass}>
                        Deskripsi
                        <textarea
                            name="description"
                            defaultValue={career?.description}
                            rows={4}
                            className={textareaClass}
                            required
                        />
                    </label>

                    <div className="grid gap-3 md:grid-cols-3">
                        {Array.from(
                            {
                                length: Math.max(
                                    4,
                                    career?.responsibilities?.length ?? 0,
                                ),
                            },
                            (_, index) => (
                                <label key={index} className={fieldClass}>
                                    Tanggung jawab {index + 1}
                                    <Input
                                        name="responsibilities[]"
                                        defaultValue={
                                            career?.responsibilities?.[index]
                                        }
                                        required
                                    />
                                </label>
                            ),
                        )}
                    </div>

                    <label className={fieldClass}>
                        Status
                        <select
                            name="is_active"
                            defaultValue={
                                career?.is_active === false ? '0' : '1'
                            }
                            className={selectClass}
                        >
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </label>

                    {Object.keys(errors).length > 0 && (
                        <p className="text-xs font-bold text-red-700">
                            Periksa kembali data karier.
                        </p>
                    )}

                    <Button disabled={processing} className="w-fit">
                        <Plus className="size-4" />
                        {career ? 'Simpan perubahan' : 'Tambah karier'}
                    </Button>
                </>
            )}
        </Form>
    );
}

function SkillForm({ skill }: { skill?: Skill }) {
    const action = skill ? `/admin/skills/${skill.slug}` : '/admin/skills';

    return (
        <Form
            action={action}
            method={skill ? 'put' : 'post'}
            className="grid gap-4"
        >
            {({ processing }) => (
                <>
                    <div className="grid gap-4 md:grid-cols-3">
                        <label className={fieldClass}>
                            Nama
                            <Input
                                name="name"
                                defaultValue={skill?.name}
                                required
                            />
                        </label>

                        <label className={fieldClass}>
                            Kategori
                            <Input
                                name="category"
                                defaultValue={skill?.category}
                                required
                            />
                        </label>

                        <label className={fieldClass}>
                            Kesulitan
                            <Input
                                name="difficulty"
                                defaultValue={skill?.difficulty ?? 'Dasar'}
                                required
                            />
                        </label>
                    </div>

                    <label className={fieldClass}>
                        Deskripsi
                        <textarea
                            name="description"
                            defaultValue={skill?.description}
                            rows={3}
                            className={textareaClass}
                            required
                        />
                    </label>

                    <Button disabled={processing} className="w-fit">
                        <Plus className="size-4" />
                        {skill ? 'Simpan perubahan' : 'Tambah skill'}
                    </Button>
                </>
            )}
        </Form>
    );
}

function AssessmentForm({
    assessment,
    careers,
}: {
    assessment?: Assessment;
    careers: Career[];
}) {
    const action = assessment
        ? `/admin/assessments/${assessment.id}`
        : '/admin/assessments';

    return (
        <Form
            action={action}
            method={assessment ? 'put' : 'post'}
            className="grid gap-4"
        >
            {({ processing }) => (
                <>
                    <div className="grid gap-4 md:grid-cols-2">
                        <label className={fieldClass}>
                            Karier
                            <select
                                name="career_id"
                                defaultValue={assessment?.career_id}
                                className={selectClass}
                                required
                            >
                                <option value="">Pilih</option>

                                {careers.map((career) => (
                                    <option key={career.id} value={career.id}>
                                        {career.name}
                                    </option>
                                ))}
                            </select>
                        </label>

                        <label className={fieldClass}>
                            Durasi menit
                            <Input
                                type="number"
                                name="duration_minutes"
                                defaultValue={
                                    assessment?.duration_minutes ?? 20
                                }
                                min={5}
                                max={180}
                                required
                            />
                        </label>
                    </div>

                    <label className={fieldClass}>
                        Judul
                        <Input
                            name="title"
                            defaultValue={assessment?.title}
                            required
                        />
                    </label>

                    <label className={fieldClass}>
                        Deskripsi
                        <textarea
                            name="description"
                            defaultValue={assessment?.description}
                            rows={3}
                            className={textareaClass}
                            required
                        />
                    </label>

                    <label className={fieldClass}>
                        Status
                        <select
                            name="is_active"
                            defaultValue={
                                assessment?.is_active === false ? '0' : '1'
                            }
                            className={selectClass}
                        >
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </label>

                    <Button disabled={processing} className="w-fit">
                        <Plus className="size-4" />
                        {assessment ? 'Simpan asesmen' : 'Tambah asesmen'}
                    </Button>
                </>
            )}
        </Form>
    );
}

function QuestionForm({
    question,
    assessments,
    skills,
}: {
    question?: Question;
    assessments: Assessment[];
    skills: Skill[];
}) {
    const action = question
        ? `/admin/questions/${question.id}`
        : '/admin/questions';

    return (
        <Form
            action={action}
            method={question ? 'put' : 'post'}
            className="grid gap-4"
        >
            {({ processing }) => (
                <>
                    <div className="grid gap-4 md:grid-cols-3">
                        <label className={fieldClass}>
                            Asesmen
                            <select
                                name="assessment_id"
                                defaultValue={question?.assessment_id}
                                className={selectClass}
                                required
                            >
                                <option value="">Pilih</option>

                                {assessments.map((item) => (
                                    <option key={item.id} value={item.id}>
                                        {item.title}
                                    </option>
                                ))}
                            </select>
                        </label>

                        <label className={fieldClass}>
                            Skill
                            <select
                                name="skill_id"
                                defaultValue={question?.skill_id}
                                className={selectClass}
                                required
                            >
                                <option value="">Pilih</option>

                                {skills.map((skill) => (
                                    <option key={skill.id} value={skill.id}>
                                        {skill.name}
                                    </option>
                                ))}
                            </select>
                        </label>

                        <label className={fieldClass}>
                            Kesulitan
                            <Input
                                name="difficulty"
                                defaultValue={question?.difficulty ?? 'Dasar'}
                                required
                            />
                        </label>
                    </div>

                    <label className={fieldClass}>
                        Pertanyaan
                        <textarea
                            name="prompt"
                            defaultValue={question?.prompt}
                            rows={3}
                            className={textareaClass}
                            required
                        />
                    </label>

                    <div className="grid gap-3 md:grid-cols-2">
                        {(['A', 'B', 'C', 'D'] as const).map((letter) => (
                            <label key={letter} className={fieldClass}>
                                Opsi {letter}
                                <Input
                                    name="options[]"
                                    defaultValue={question?.options?.[letter]}
                                    required
                                />
                            </label>
                        ))}
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        <label className={fieldClass}>
                            Jawaban
                            <select
                                name="correct_answer"
                                defaultValue={question?.correct_answer ?? 'A'}
                                className={selectClass}
                            >
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </label>

                        <label className={fieldClass}>
                            Penjelasan
                            <Input
                                name="explanation"
                                defaultValue={question?.explanation ?? ''}
                            />
                        </label>
                    </div>

                    <Button disabled={processing} className="w-fit">
                        <Plus className="size-4" />
                        {question ? 'Simpan soal' : 'Tambah soal'}
                    </Button>
                </>
            )}
        </Form>
    );
}

function MaterialForm({
    material,
    skills,
}: {
    material?: Material;
    skills: Skill[];
}) {
    const action = material
        ? `/admin/materials/${material.slug}`
        : '/admin/materials';

    return (
        <Form
            action={action}
            method={material ? 'put' : 'post'}
            className="grid gap-4"
        >
            {({ processing }) => (
                <>
                    <div className="grid gap-4 md:grid-cols-3">
                        <label className={fieldClass}>
                            Skill
                            <select
                                name="skill_id"
                                defaultValue={material?.skill_id}
                                className={selectClass}
                                required
                            >
                                <option value="">Pilih</option>

                                {skills.map((skill) => (
                                    <option key={skill.id} value={skill.id}>
                                        {skill.name}
                                    </option>
                                ))}
                            </select>
                        </label>

                        <label className={fieldClass}>
                            Kesulitan
                            <Input
                                name="difficulty"
                                defaultValue={material?.difficulty ?? 'Dasar'}
                                required
                            />
                        </label>

                        <label className={fieldClass}>
                            Estimasi menit
                            <Input
                                type="number"
                                name="estimated_minutes"
                                min={15}
                                defaultValue={material?.estimated_minutes ?? 90}
                                required
                            />
                        </label>
                    </div>

                    <label className={fieldClass}>
                        Judul
                        <Input
                            name="title"
                            defaultValue={material?.title}
                            required
                        />
                    </label>

                    <label className={fieldClass}>
                        Ringkasan
                        <textarea
                            name="summary"
                            defaultValue={material?.summary}
                            rows={3}
                            className={textareaClass}
                            required
                        />
                    </label>

                    <div className="grid gap-3 md:grid-cols-3">
                        {Array.from(
                            {
                                length: Math.max(
                                    3,
                                    material?.learning_objectives?.length ?? 0,
                                ),
                            },
                            (_, index) => (
                                <label key={index} className={fieldClass}>
                                    Tujuan {index + 1}
                                    <Input
                                        name="learning_objectives[]"
                                        defaultValue={
                                            material?.learning_objectives?.[
                                                index
                                            ]
                                        }
                                        required
                                    />
                                </label>
                            ),
                        )}
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        <label className={fieldClass}>
                            Judul referensi
                            <Input
                                name="resource_title"
                                defaultValue={material?.resource_title ?? ''}
                            />
                        </label>

                        <label className={fieldClass}>
                            URL referensi
                            <Input
                                type="url"
                                name="resource_url"
                                defaultValue={material?.resource_url ?? ''}
                            />
                        </label>
                    </div>

                    <label className={fieldClass}>
                        Latihan praktik
                        <textarea
                            name="practice_task"
                            defaultValue={material?.practice_task}
                            rows={3}
                            className={textareaClass}
                            required
                        />
                    </label>

                    <label className={fieldClass}>
                        Pertanyaan evaluasi
                        <textarea
                            name="quiz_question"
                            defaultValue={material?.quiz_question}
                            rows={2}
                            className={textareaClass}
                            required
                        />
                    </label>

                    <div className="grid gap-3 md:grid-cols-2">
                        {(['A', 'B', 'C', 'D'] as const).map((letter) => (
                            <label key={letter} className={fieldClass}>
                                Opsi {letter}
                                <Input
                                    name="quiz_options[]"
                                    defaultValue={
                                        material?.quiz_options?.[letter]
                                    }
                                    required
                                />
                            </label>
                        ))}
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        <label className={fieldClass}>
                            Jawaban
                            <select
                                name="quiz_answer"
                                defaultValue={material?.quiz_answer ?? 'A'}
                                className={selectClass}
                            >
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </label>

                        <label className={fieldClass}>
                            Penjelasan jawaban
                            <Input
                                name="quiz_explanation"
                                defaultValue={material?.quiz_explanation ?? ''}
                            />
                        </label>
                    </div>

                    <Button disabled={processing} className="w-fit">
                        <Plus className="size-4" />
                        {material ? 'Simpan materi' : 'Tambah materi'}
                    </Button>
                </>
            )}
        </Form>
    );
}

function ProjectForm({
    project,
    careers,
}: {
    project?: Project;
    careers: Career[];
}) {
    const action = project
        ? `/admin/projects/${project.slug}`
        : '/admin/projects';

    return (
        <Form
            action={action}
            method={project ? 'put' : 'post'}
            className="grid gap-4"
        >
            {({ processing }) => (
                <>
                    <div className="grid gap-4 md:grid-cols-3">
                        <label className={fieldClass}>
                            Karier
                            <select
                                name="career_id"
                                defaultValue={project?.career_id}
                                className={selectClass}
                                required
                            >
                                <option value="">Pilih</option>

                                {careers.map((career) => (
                                    <option key={career.id} value={career.id}>
                                        {career.name}
                                    </option>
                                ))}
                            </select>
                        </label>

                        <label className={fieldClass}>
                            Kesulitan
                            <Input
                                name="difficulty"
                                defaultValue={project?.difficulty ?? 'Pemula'}
                                required
                            />
                        </label>

                        <label className={fieldClass}>
                            Estimasi jam
                            <Input
                                type="number"
                                name="estimated_hours"
                                min={1}
                                defaultValue={project?.estimated_hours ?? 8}
                                required
                            />
                        </label>
                    </div>

                    <label className={fieldClass}>
                        Judul
                        <Input
                            name="title"
                            defaultValue={project?.title}
                            required
                        />
                    </label>

                    <label className={fieldClass}>
                        Ringkasan
                        <textarea
                            name="summary"
                            defaultValue={project?.summary}
                            rows={3}
                            className={textareaClass}
                            required
                        />
                    </label>

                    <label className={fieldClass}>
                        Problem statement
                        <textarea
                            name="problem_statement"
                            defaultValue={project?.problem_statement}
                            rows={3}
                            className={textareaClass}
                            required
                        />
                    </label>

                    {[
                        [
                            'minimum_features',
                            'Fitur minimum',
                            project?.minimum_features,
                        ],
                        [
                            'stretch_features',
                            'Fitur tambahan',
                            project?.stretch_features,
                        ],
                        [
                            'completion_criteria',
                            'Kriteria selesai',
                            project?.completion_criteria,
                        ],
                    ].map(([name, label, values]) => (
                        <div key={String(name)}>
                            <p className="mb-2 text-sm font-black">
                                {String(label)}
                            </p>

                            <div className="grid gap-3 md:grid-cols-3">
                                {Array.from(
                                    {
                                        length: Math.max(
                                            3,
                                            Array.isArray(values)
                                                ? values.length
                                                : 0,
                                        ),
                                    },
                                    (_, index) => (
                                        <Input
                                            key={index}
                                            name={`${name}[]`}
                                            defaultValue={
                                                Array.isArray(values)
                                                    ? values[index]
                                                    : ''
                                            }
                                            required={
                                                name !== 'stretch_features'
                                            }
                                        />
                                    ),
                                )}
                            </div>
                        </div>
                    ))}

                    <Button disabled={processing} className="w-fit">
                        <Plus className="size-4" />
                        {project ? 'Simpan proyek' : 'Tambah proyek'}
                    </Button>
                </>
            )}
        </Form>
    );
}

export default function AdminIndex({
    stats,
    careers,
    skills,
    prerequisites,
    assessments,
    materials,
    projects,
}: {
    stats: {
        users: number;
        careers: number;
        skills: number;
        materials: number;
        projects: number;
        assessmentAttempts: number;
    };
    careers: Career[];
    skills: Skill[];
    prerequisites: Prerequisite[];
    assessments: Assessment[];
    materials: Material[];
    projects: Project[];
}) {
    return (
        <>
            <Head title="Admin" />

            <div className="mx-auto flex w-full max-w-7xl flex-col gap-6 p-4 md:p-8">
                <section className="neo-card bg-[var(--neo-orange)] p-6 md:p-8">
                    <div className="flex items-start gap-4">
                        <ShieldCheck className="size-10" />
                        <div>
                            <span className="neo-label bg-white">
                                Admin workspace
                            </span>
                            <h1 className="neo-heading mt-4 text-4xl md:text-5xl">
                                Kelola standar, bukan hasil pengguna.
                            </h1>
                            <p className="mt-3 max-w-2xl text-sm leading-6 font-medium">
                                Data profesi, skill, dependency, assessment,
                                materi, dan project dikelola di sini. AI tidak
                                boleh mengubah data kompetensi pengguna.
                            </p>
                        </div>
                    </div>
                </section>

                <section className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                    {[
                        ['Mahasiswa', stats.users, UsersRound],
                        ['Karier', stats.careers, BriefcaseBusiness],
                        ['Skill', stats.skills, Layers3],
                        ['Materi', stats.materials, BookOpenCheck],
                        ['Projects', stats.projects, Wrench],
                        ['Attempts', stats.assessmentAttempts, Database],
                    ].map(([label, value, Icon]) => {
                        const IconComponent = Icon as typeof UsersRound;

                        return (
                            <Card key={String(label)}>
                                <CardContent className="pt-6">
                                    <IconComponent className="size-5" />
                                    <p className="mt-4 text-3xl font-black">
                                        {String(value)}
                                    </p>
                                    <p className="mt-1 text-xs font-black uppercase">
                                        {String(label)}
                                    </p>
                                </CardContent>
                            </Card>
                        );
                    })}
                </section>

                <Card>
                    <CardHeader className="border-b-2 border-black bg-[var(--neo-lime)]">
                        <CardTitle className="text-2xl font-black">
                            Karier & standar skill
                        </CardTitle>
                    </CardHeader>

                    <CardContent className="space-y-6 pt-6">
                        <details className="neo-card-flat bg-[var(--neo-cream)] p-4">
                            <summary className="cursor-pointer font-black">
                                Tambah karier
                            </summary>

                            <div className="mt-5">
                                <CareerForm />
                            </div>
                        </details>

                        <div className="grid gap-4">
                            {careers.map((career) => (
                                <details
                                    key={career.id}
                                    className="border-2 border-black bg-white p-4"
                                >
                                    <summary className="cursor-pointer text-lg font-black">
                                        {career.name}{' '}
                                        <span className="ml-2 text-xs text-black/50">
                                            {career.skills.length} skill
                                        </span>
                                    </summary>

                                    <div className="mt-5 grid gap-6 border-t-2 border-black/15 pt-5">
                                        <CareerForm career={career} />

                                        <div>
                                            <p className="mb-3 text-sm font-black uppercase">
                                                Tambahkan standar skill
                                            </p>

                                            <Form
                                                action={`/admin/careers/${career.slug}/skills`}
                                                method="post"
                                                className="grid gap-3 md:grid-cols-4"
                                            >
                                                {({ processing }) => (
                                                    <>
                                                        <select
                                                            name="skill_id"
                                                            className={
                                                                selectClass
                                                            }
                                                            required
                                                        >
                                                            <option value="">
                                                                Pilih skill
                                                            </option>

                                                            {skills.map(
                                                                (skill) => (
                                                                    <option
                                                                        key={
                                                                            skill.id
                                                                        }
                                                                        value={
                                                                            skill.id
                                                                        }
                                                                    >
                                                                        {
                                                                            skill.name
                                                                        }
                                                                    </option>
                                                                ),
                                                            )}
                                                        </select>

                                                        <Input
                                                            type="number"
                                                            name="target_level"
                                                            min={1}
                                                            max={100}
                                                            placeholder="Target 0-100"
                                                            required
                                                        />

                                                        <Input
                                                            type="number"
                                                            step="0.05"
                                                            name="importance_weight"
                                                            min={0.1}
                                                            max={3}
                                                            placeholder="Bobot"
                                                            required
                                                        />

                                                        <div className="flex gap-2">
                                                            <select
                                                                name="is_required"
                                                                className={
                                                                    selectClass
                                                                }
                                                            >
                                                                <option value="1">
                                                                    Wajib
                                                                </option>
                                                                <option value="0">
                                                                    Pendukung
                                                                </option>
                                                            </select>

                                                            <Button
                                                                disabled={
                                                                    processing
                                                                }
                                                            >
                                                                Simpan
                                                            </Button>
                                                        </div>
                                                    </>
                                                )}
                                            </Form>

                                            <div className="mt-4 flex flex-wrap gap-2">
                                                {career.skills.map((skill) => (
                                                    <div
                                                        key={skill.id}
                                                        className="flex items-center gap-2 border-2 border-black bg-[var(--neo-cream)] px-3 py-2 text-xs font-black"
                                                    >
                                                        {skill.name} ·{' '}
                                                        {
                                                            skill.pivot
                                                                ?.target_level
                                                        }
                                                        <DeleteButton
                                                            action={`/admin/careers/${career.slug}/skills/${skill.slug}`}
                                                            label="×"
                                                        />
                                                    </div>
                                                ))}
                                            </div>
                                        </div>

                                        <DeleteButton
                                            action={`/admin/careers/${career.slug}`}
                                        />
                                    </div>
                                </details>
                            ))}
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader className="border-b-2 border-black bg-[var(--neo-blue)]">
                        <CardTitle className="text-2xl font-black">
                            Skill & prasyarat
                        </CardTitle>
                    </CardHeader>

                    <CardContent className="space-y-6 pt-6">
                        <details className="neo-card-flat bg-[var(--neo-cream)] p-4">
                            <summary className="cursor-pointer font-black">
                                Tambah skill
                            </summary>
                            <div className="mt-5">
                                <SkillForm />
                            </div>
                        </details>

                        <div className="grid gap-3 md:grid-cols-2">
                            {skills.map((skill) => (
                                <details
                                    key={skill.id}
                                    className="border-2 border-black bg-white p-4"
                                >
                                    <summary className="cursor-pointer font-black">
                                        {skill.name}
                                        <span className="ml-2 text-xs text-black/50">
                                            {skill.category}
                                        </span>
                                    </summary>

                                    <div className="mt-5 grid gap-4 border-t-2 border-black/15 pt-5">
                                        <SkillForm skill={skill} />

                                        <DeleteButton
                                            action={`/admin/skills/${skill.slug}`}
                                        />
                                    </div>
                                </details>
                            ))}
                        </div>

                        <div className="border-t-2 border-black pt-6">
                            <h3 className="text-lg font-black">
                                Dependency graph
                            </h3>

                            <Form
                                action="/admin/prerequisites"
                                method="post"
                                className="mt-4 grid gap-3 md:grid-cols-4"
                            >
                                {({ processing }) => (
                                    <>
                                        <select
                                            name="skill_id"
                                            className={selectClass}
                                            required
                                        >
                                            <option value="">
                                                Skill tujuan
                                            </option>

                                            {skills.map((skill) => (
                                                <option
                                                    key={skill.id}
                                                    value={skill.id}
                                                >
                                                    {skill.name}
                                                </option>
                                            ))}
                                        </select>

                                        <select
                                            name="prerequisite_skill_id"
                                            className={selectClass}
                                            required
                                        >
                                            <option value="">Prasyarat</option>

                                            {skills.map((skill) => (
                                                <option
                                                    key={skill.id}
                                                    value={skill.id}
                                                >
                                                    {skill.name}
                                                </option>
                                            ))}
                                        </select>

                                        <Input
                                            type="number"
                                            step="0.05"
                                            min={1}
                                            max={2}
                                            name="factor"
                                            defaultValue="1.15"
                                            required
                                        />

                                        <Button disabled={processing}>
                                            <Plus className="size-4" />
                                            Simpan relasi
                                        </Button>
                                    </>
                                )}
                            </Form>

                            <div className="mt-4 grid gap-2 md:grid-cols-2">
                                {prerequisites.map((item) => (
                                    <div
                                        key={item.id}
                                        className="flex items-center justify-between gap-3 border-2 border-black bg-[var(--neo-cream)] p-3 text-sm"
                                    >
                                        <span>
                                            <b>{item.prerequisite_name}</b> →{' '}
                                            {item.skill_name} · ×{item.factor}
                                        </span>

                                        <DeleteButton
                                            action={`/admin/prerequisites/${item.id}`}
                                            label="×"
                                        />
                                    </div>
                                ))}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader className="border-b-2 border-black bg-[var(--neo-orange)]">
                        <CardTitle className="text-2xl font-black">
                            Asesmen & soal
                        </CardTitle>
                    </CardHeader>

                    <CardContent className="space-y-6 pt-6">
                        <details className="neo-card-flat bg-[var(--neo-cream)] p-4">
                            <summary className="cursor-pointer font-black">
                                Tambah asesmen
                            </summary>
                            <div className="mt-5">
                                <AssessmentForm careers={careers} />
                            </div>
                        </details>

                        {assessments.map((assessment) => (
                            <details
                                key={assessment.id}
                                className="border-2 border-black bg-white p-4"
                            >
                                <summary className="cursor-pointer font-black">
                                    {assessment.title}

                                    <span className="ml-2 text-xs text-black/50">
                                        {assessment.questions.length} soal
                                    </span>
                                </summary>

                                <div className="mt-5 grid gap-6 border-t-2 border-black/15 pt-5">
                                    <AssessmentForm
                                        assessment={assessment}
                                        careers={careers}
                                    />

                                    <details className="border-2 border-black bg-[var(--neo-cream)] p-4">
                                        <summary className="cursor-pointer font-black">
                                            Tambah soal
                                        </summary>

                                        <div className="mt-5">
                                            <QuestionForm
                                                assessments={assessments}
                                                skills={skills}
                                            />
                                        </div>
                                    </details>

                                    <div className="grid gap-3">
                                        {assessment.questions.map(
                                            (question) => (
                                                <details
                                                    key={question.id}
                                                    className="border-2 border-black/30 p-3"
                                                >
                                                    <summary className="cursor-pointer text-sm font-black">
                                                        {question.skill?.name}:{' '}
                                                        {question.prompt}
                                                    </summary>

                                                    <div className="mt-4 grid gap-4">
                                                        <QuestionForm
                                                            question={question}
                                                            assessments={
                                                                assessments
                                                            }
                                                            skills={skills}
                                                        />

                                                        <DeleteButton
                                                            action={`/admin/questions/${question.id}`}
                                                        />
                                                    </div>
                                                </details>
                                            ),
                                        )}
                                    </div>

                                    <DeleteButton
                                        action={`/admin/assessments/${assessment.id}`}
                                    />
                                </div>
                            </details>
                        ))}
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader className="border-b-2 border-black bg-[var(--neo-yellow)]">
                        <CardTitle className="text-2xl font-black">
                            Materi belajar
                        </CardTitle>
                    </CardHeader>

                    <CardContent className="space-y-5 pt-6">
                        <details className="neo-card-flat bg-[var(--neo-cream)] p-4">
                            <summary className="cursor-pointer font-black">
                                Tambah materi
                            </summary>

                            <div className="mt-5">
                                <MaterialForm skills={skills} />
                            </div>
                        </details>

                        <div className="grid gap-3">
                            {materials.map((material) => (
                                <details
                                    key={material.id}
                                    className="border-2 border-black bg-white p-4"
                                >
                                    <summary className="cursor-pointer font-black">
                                        {material.title}

                                        <span className="ml-2 text-xs text-black/50">
                                            {material.skill?.name} ·{' '}
                                            {material.estimated_minutes}m
                                        </span>
                                    </summary>

                                    <div className="mt-5 grid gap-4 border-t-2 border-black/15 pt-5">
                                        <MaterialForm
                                            material={material}
                                            skills={skills}
                                        />

                                        <DeleteButton
                                            action={`/admin/materials/${material.slug}`}
                                        />
                                    </div>
                                </details>
                            ))}
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader className="border-b-2 border-black bg-[var(--neo-lime)]">
                        <CardTitle className="text-2xl font-black">
                            Portfolio projects
                        </CardTitle>
                    </CardHeader>

                    <CardContent className="space-y-5 pt-6">
                        <details className="neo-card-flat bg-[var(--neo-cream)] p-4">
                            <summary className="cursor-pointer font-black">
                                Tambah proyek
                            </summary>

                            <div className="mt-5">
                                <ProjectForm careers={careers} />
                            </div>
                        </details>

                        {projects.map((project) => (
                            <details
                                key={project.id}
                                className="border-2 border-black bg-white p-4"
                            >
                                <summary className="cursor-pointer font-black">
                                    {project.title}

                                    <span className="ml-2 text-xs text-black/50">
                                        {project.career?.name} ·{' '}
                                        {project.skills.length} requirement
                                    </span>
                                </summary>

                                <div className="mt-5 grid gap-6 border-t-2 border-black/15 pt-5">
                                    <ProjectForm
                                        project={project}
                                        careers={careers}
                                    />

                                    <div>
                                        <p className="mb-3 text-sm font-black uppercase">
                                            Skill readiness proyek
                                        </p>

                                        <Form
                                            action={`/admin/projects/${project.slug}/skills`}
                                            method="post"
                                            className="grid gap-3 md:grid-cols-4"
                                        >
                                            {({ processing }) => (
                                                <>
                                                    <select
                                                        name="skill_id"
                                                        className={selectClass}
                                                        required
                                                    >
                                                        <option value="">
                                                            Pilih skill
                                                        </option>

                                                        {skills.map((skill) => (
                                                            <option
                                                                key={skill.id}
                                                                value={skill.id}
                                                            >
                                                                {skill.name}
                                                            </option>
                                                        ))}
                                                    </select>

                                                    <Input
                                                        type="number"
                                                        name="required_level"
                                                        min={1}
                                                        max={100}
                                                        placeholder="Level minimum"
                                                        required
                                                    />

                                                    <Input
                                                        type="number"
                                                        step="0.1"
                                                        name="weight"
                                                        min={0.1}
                                                        max={3}
                                                        placeholder="Bobot"
                                                        required
                                                    />

                                                    <Button
                                                        disabled={processing}
                                                    >
                                                        Simpan requirement
                                                    </Button>
                                                </>
                                            )}
                                        </Form>

                                        <div className="mt-4 flex flex-wrap gap-2">
                                            {project.skills.map((skill) => (
                                                <div
                                                    key={skill.id}
                                                    className="flex items-center gap-2 border-2 border-black bg-[var(--neo-cream)] px-3 py-2 text-xs font-black"
                                                >
                                                    {skill.name} ·{' '}
                                                    {
                                                        skill.pivot
                                                            ?.required_level
                                                    }
                                                    <DeleteButton
                                                        action={`/admin/projects/${project.slug}/skills/${skill.slug}`}
                                                        label="×"
                                                    />
                                                </div>
                                            ))}
                                        </div>
                                    </div>

                                    <DeleteButton
                                        action={`/admin/projects/${project.slug}`}
                                    />
                                </div>
                            </details>
                        ))}
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

AdminIndex.layout = {
    breadcrumbs: [
        {
            title: 'Admin',
            href: '/admin',
        },
    ],
};
