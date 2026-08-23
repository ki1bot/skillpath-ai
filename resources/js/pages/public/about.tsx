import { Head } from '@inertiajs/react';
import {
    Bot,
    Database,
    GitBranch,
    GraduationCap,
    ShieldCheck,
} from 'lucide-react';
import type { LucideIcon } from 'lucide-react';

type Principle = {
    icon: LucideIcon;
    title: string;
    text: string;
};

const principles: Principle[] = [
    {
        icon: GraduationCap,
        title: 'Berangkat dari jurusan',
        text: 'SkillPath menggunakan lima jurusan sebagai titik awal: Sistem Informasi, Manajemen, Teknik Informatika, Psikologi, dan Ilmu Komunikasi. Setiap jurusan memiliki tiga bidang dan sembilan kemampuan yang dinilai.',
    },
    {
        icon: Database,
        title: 'Kemampuan disimpan sebagai data',
        text: 'Target penguasaan, bobot, hasil asesmen, materi, evaluasi, dan progres disimpan sebagai data agar rekomendasi dapat diperiksa kembali.',
    },
    {
        icon: GitBranch,
        title: 'Belajar mengikuti kebutuhan',
        text: 'Kemampuan yang masih jauh dari target dapat ditempatkan lebih awal agar kamu tidak perlu mempelajari semuanya sekaligus.',
    },
    {
        icon: ShieldCheck,
        title: 'Perkembangan harus terlihat',
        text: 'Menyelesaikan materi saja belum cukup. Asesmen, evaluasi, aktivitas belajar, dan proyek ikut digunakan untuk melihat perkembanganmu.',
    },
    {
        icon: Bot,
        title: 'AI membantu menjelaskan',
        text: 'AI digunakan untuk membantu menjelaskan hasil yang sudah dihitung sistem. AI tidak menentukan nilai asesmen dan tidak boleh membuat kemampuan atau hasil baru di luar data yang tersedia.',
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
                        Membantu mahasiswa memahami kemampuan dan menentukan apa
                        yang perlu dipelajari berikutnya.
                    </h1>

                    <p className="mt-6 text-lg leading-relaxed font-medium text-muted-foreground">
                        SkillPath AI menggunakan jurusan sebagai dasar pemetaan
                        kemampuan. Mahasiswa memilih jurusan, mengerjakan
                        asesmen, melihat hasil kemampuan, lalu mendapatkan jalur
                        belajar yang disusun berdasarkan bagian yang masih perlu
                        dikembangkan.
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
                        Skor SkillPath adalah alat bantu untuk membaca
                        perkembangan.
                    </h2>

                    <p className="mt-3 max-w-3xl leading-relaxed font-semibold">
                        Skor 0–100 membantu melihat perkembangan kemampuan,
                        penyelesaian jalur belajar, proyek, konsistensi, dan
                        evaluasi. Angka tersebut bukan nilai akademik resmi dan
                        bukan jaminan hasil tertentu di dunia kerja.
                    </p>
                </div>
            </main>
        </>
    );
}
