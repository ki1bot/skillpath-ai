import { Head, Link } from '@inertiajs/react';
import { ArrowRight, CheckCircle2 } from 'lucide-react';

type Skill = {
    id: number;
    name: string;
    category: string;
    pivot: {
        target_level: number;
        importance_weight: string;
        is_required: boolean;
    };
};

type Career = {
    id: number;
    name: string;
    slug: string;
    tagline: string;
    description: string;
    difficulty: string;
    accent: string;
    skills: Skill[];
};

export default function Careers({ careers }: { careers: Career[] }) {
    return (
        <>
            <Head title="Jalur Karier" />

            <main className="neo-page py-14 lg:py-20">
                <div className="max-w-3xl">
                    <span className="neo-label">Jelajahi karier</span>

                    <h1 className="neo-heading mt-6 text-5xl sm:text-6xl">
                        Pahami pekerjaannya sebelum memilih jalurnya.
                    </h1>

                    <p className="mt-5 text-lg leading-relaxed font-medium text-muted-foreground">
                        Lihat tanggung jawab dan kemampuan yang dibutuhkan.
                        Setelah penilaian, kamu bisa melihat seberapa jauh
                        posisi sekarang dari target yang dipilih.
                    </p>
                </div>

                <div className="mt-12 space-y-6">
                    {careers.map((career, index) => (
                        <article
                            key={career.id}
                            className="neo-card grid overflow-hidden lg:grid-cols-[220px_1fr_auto]"
                        >
                            <div
                                className="flex min-h-44 flex-col justify-between border-b-2 border-[#171717] p-6 text-[#171717] lg:border-r-2 lg:border-b-0"
                                style={{
                                    backgroundColor: career.accent,
                                }}
                            >
                                <span className="font-mono text-sm font-black">
                                    {String(index + 1).padStart(2, '0')}
                                </span>

                                <div>
                                    <p className="text-xs font-black tracking-[0.14em] uppercase">
                                        Tingkat kesulitan
                                    </p>

                                    <p className="mt-1 text-xl font-black">
                                        {career.difficulty}
                                    </p>
                                </div>
                            </div>

                            <div className="p-6 sm:p-8">
                                <h2 className="text-3xl font-black tracking-[-0.035em]">
                                    {career.name}
                                </h2>

                                <p className="mt-2 max-w-3xl leading-relaxed font-bold">
                                    {career.tagline}
                                </p>

                                <p className="mt-4 max-w-3xl text-sm leading-relaxed font-medium text-muted-foreground">
                                    {career.description}
                                </p>

                                <div className="mt-6 flex flex-wrap gap-2">
                                    {career.skills.slice(0, 6).map((skill) => (
                                        <span
                                            key={skill.id}
                                            className="inline-flex items-center gap-1.5 rounded-full border-2 border-foreground bg-muted px-3 py-1 text-xs font-bold"
                                        >
                                            <CheckCircle2 className="size-3.5" />
                                            {skill.name}
                                        </span>
                                    ))}

                                    {career.skills.length > 6 && (
                                        <span className="rounded-full border-2 border-foreground px-3 py-1 text-xs font-black">
                                            +{career.skills.length - 6} lainnya
                                        </span>
                                    )}
                                </div>
                            </div>

                            <div className="flex items-end p-6 pt-0 lg:p-8 lg:pl-0">
                                <Link
                                    href={`/karier/${career.slug}`}
                                    className="inline-flex items-center gap-2 border-b-2 border-foreground pb-1 text-sm font-black"
                                >
                                    Lihat detail
                                    <ArrowRight className="size-4" />
                                </Link>
                            </div>
                        </article>
                    ))}
                </div>
            </main>
        </>
    );
}
