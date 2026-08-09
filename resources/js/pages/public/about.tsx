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
        title: 'Data kemampuan yang jelas',
        text: 'Karier, target kemampuan, bobot, prasyarat, materi, dan proyek disimpan sebagai data yang dapat dikelola dan diperiksa kembali.',
    },
    {
        icon: GitBranch,
        title: 'Urutan belajar mengikuti prasyarat',
        text: 'Kemampuan dasar ditempatkan lebih awal ketika dibutuhkan untuk memahami kemampuan lain.',
    },
    {
        icon: ShieldCheck,
        title: 'Perkembangan harus terlihat',
        text: 'Menyelesaikan materi saja belum cukup. Evaluasi dan aktivitas belajar ikut digunakan untuk melihat perkembangan.',
    },
    {
        icon: Bot,
        title: 'AI hanya membantu menjelaskan',
        text: 'AI membantu merangkum hasil agar lebih mudah dipahami. Asesmen, perhitungan kemampuan, jalur belajar, proyek, dan skor kesiapan tetap berasal dari data dan aturan sistem.',
    },
];

export default function About() {
    return (
        <>
            <Head title="Tentang SkillPath AI" />

            <main className="neo-page py-14 lg:py-20">
                <div className="max-w-4xl">
                    <span className="neo-label">Tentang SkillPath</span>

                    <h1 className="neo-heading mt-6 text-5xl sm:text-6xl">
                        Rekomendasi boleh pintar, tapi tetap harus bisa
                        dijelaskan.
                    </h1>

                    <p className="mt-6 text-lg leading-relaxed font-medium text-muted-foreground">
                        SkillPath AI membantu mahasiswa yang sudah punya tujuan,
                        tetapi masih bingung menentukan kemampuan mana yang
                        perlu dipelajari lebih dahulu.
                    </p>
                </div>

                <div className="mt-12 grid gap-5 md:grid-cols-2">
                    {principles.map(({ icon: Icon, title, text }) => (
                        <article
                            key={title}
                            className="neo-card neo-interactive p-6"
                        >
                            <span className="flex size-12 items-center justify-center rounded-[11px] border-2 border-foreground bg-muted">
                                <Icon className="size-6" />
                            </span>

                            <h2 className="mt-7 text-2xl font-black tracking-tight">
                                {title}
                            </h2>

                            <p className="mt-3 text-sm leading-relaxed font-medium text-muted-foreground">
                                {text}
                            </p>
                        </article>
                    ))}
                </div>

                <div className="mt-12 rounded-[16px] border-2 border-[#171717] bg-secondary p-7 text-[#171717] shadow-[5px_5px_0_#171717] sm:p-9">
                    <p className="text-xs font-black tracking-[0.16em] uppercase">
                        Perlu diingat
                    </p>

                    <h2 className="mt-2 text-3xl font-black tracking-tight">
                        Skor kesiapan karier bukan ramalan diterima kerja.
                    </h2>

                    <p className="mt-3 max-w-3xl leading-relaxed font-semibold">
                        Skor 0–100 dipakai untuk melihat perkembangan dari
                        kemampuan, penyelesaian jalur belajar, proyek,
                        konsistensi, dan hasil evaluasi. Angka ini membantu
                        menentukan langkah berikutnya, bukan menjanjikan hasil
                        rekrutmen.
                    </p>
                </div>
            </main>
        </>
    );
}
