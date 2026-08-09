import { Head } from '@inertiajs/react';
import { Bot, Database, GitBranch, ShieldCheck } from 'lucide-react';
import type { LucideIcon } from 'lucide-react';

type Principle = {
    icon: LucideIcon;
    title: string;
    text: string;
};

const principles: Principle[] = [
    {
        icon: Database,
        title: 'Basis data skill yang nyata',
        text: 'Profesi, target skill, bobot, prasyarat, materi, dan proyek disimpan sebagai data yang dapat dikelola administrator.',
    },
    {
        icon: GitBranch,
        title: 'Roadmap mengikuti dependensi',
        text: 'Skill fondasi ditempatkan lebih awal ketika dibutuhkan oleh skill lain. Urutan tidak dibuat dari prompt kosong.',
    },
    {
        icon: ShieldCheck,
        title: 'Progres memerlukan bukti',
        text: 'Tombol selesai tidak cukup. Evaluasi dan aktivitas belajar ikut menentukan apakah skill benar-benar bergerak.',
    },
    {
        icon: Bot,
        title: 'AI punya batas',
        text: 'AI dipakai untuk merangkum dan menjelaskan hasil. Jika API tidak tersedia, asesmen, gap, roadmap, proyek, dan perhitungan kesiapan tetap berjalan.',
    },
];

export default function About() {
    return (
        <>
            <Head title="Tentang SkillPath AI" />

            <main className="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
                <div className="max-w-4xl">
                    <span className="neo-label">Tentang sistem</span>

                    <h1 className="neo-heading mt-6 text-5xl sm:text-6xl">
                        AI membantu menjelaskan. Data dan aturan tetap
                        memutuskan.
                    </h1>

                    <p className="mt-6 text-lg leading-relaxed font-medium text-muted-foreground">
                        SkillPath AI dibuat untuk mahasiswa yang tahu ingin
                        berkembang, tetapi belum punya cara objektif untuk
                        menentukan apa yang perlu dipelajari lebih dulu.
                    </p>
                </div>

                <div className="mt-12 grid gap-5 md:grid-cols-2">
                    {principles.map(({ icon: Icon, title, text }) => (
                        <article key={title} className="neo-card p-6">
                            <Icon className="size-7" />

                            <h2 className="mt-7 text-2xl font-black tracking-tight">
                                {title}
                            </h2>

                            <p className="mt-3 text-sm leading-relaxed font-medium text-muted-foreground">
                                {text}
                            </p>
                        </article>
                    ))}
                </div>

                <div className="mt-12 rounded-2xl border-2 border-foreground bg-secondary p-7 text-[#171717] shadow-[5px_5px_0_#171717] sm:p-9">
                    <p className="text-xs font-black tracking-[0.16em] uppercase">
                        Batas interpretasi
                    </p>

                    <h2 className="mt-2 text-3xl font-black tracking-tight">
                        Skor Kesiapan Karier bukan prediksi diterima kerja.
                    </h2>

                    <p className="mt-3 max-w-3xl leading-relaxed font-semibold">
                        Skor 0–100 hanya indikator internal berdasarkan
                        penguasaan skill, penyelesaian roadmap, proyek,
                        konsistensi belajar, dan evaluasi. Ia berguna untuk
                        melihat perubahan, bukan memberi janji karier.
                    </p>
                </div>
            </main>
        </>
    );
}
