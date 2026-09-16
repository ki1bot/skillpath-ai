import { Head } from '@inertiajs/react';
import PublicBackLink from '@/components/public-back-link';

const sections = [
    {
        title: 'Penggunaan layanan',
        content:
            'SkillPath AI disediakan sebagai alat bantu pembelajaran dan pemetaan kemampuan. Pengguna bertanggung jawab atas informasi yang dimasukkan ke dalam akun dan penggunaan layanan secara wajar.',
    },
    {
        title: 'Akun pengguna',
        content:
            'Pengguna bertanggung jawab menjaga keamanan akses ke akun. Login melalui email, Google, atau Facebook yang telah ditautkan akan mengarah ke akun SkillPath AI yang terkait.',
    },
    {
        title: 'Assessment dan rekomendasi',
        content:
            'Hasil assessment, skor kemampuan, jalur belajar, rekomendasi proyek, dan informasi lain yang diberikan SkillPath AI merupakan alat bantu pembelajaran dan bukan nilai akademik resmi, sertifikasi profesional, atau jaminan hasil karier.',
    },
    {
        title: 'Fitur AI',
        content:
            'Fitur AI digunakan untuk membantu memberikan penjelasan berdasarkan data yang tersedia pada sistem. Output AI dapat memiliki keterbatasan dan harus digunakan sebagai informasi pendukung, bukan satu-satunya dasar pengambilan keputusan.',
    },
    {
        title: 'Konten dan bukti pengguna',
        content:
            'Pengguna bertanggung jawab memastikan bahwa tautan, jawaban, bukti, atau materi yang diberikan melalui layanan dapat digunakan secara sah dan tidak melanggar hak pihak lain.',
    },
    {
        title: 'Keamanan layanan',
        content:
            'Pengguna tidak diperbolehkan mencoba mengakses akun milik orang lain, melewati mekanisme keamanan, mengganggu layanan, atau menggunakan sistem untuk aktivitas yang melanggar hukum.',
    },
    {
        title: 'Penghentian akun',
        content:
            'Pengguna dapat menghapus akun melalui pengaturan akun. Akses juga dapat dibatasi apabila penggunaan layanan membahayakan keamanan sistem atau melanggar ketentuan penggunaan.',
    },
    {
        title: 'Perubahan layanan',
        content:
            'Fitur dan ketentuan SkillPath AI dapat diperbarui seiring pengembangan aplikasi. Versi ketentuan yang berlaku ditampilkan pada halaman ini.',
    },
];

export default function Terms() {
    return (
        <>
            <Head title="Ketentuan Layanan" />

            <main className="neo-page py-14 lg:py-20">
                <div className="mx-auto max-w-4xl">
                    <PublicBackLink />

                    <div className="mt-8">
                        <span className="neo-label">Ketentuan</span>

                        <h1 className="neo-heading mt-6 text-5xl sm:text-6xl">
                            Ketentuan Layanan SkillPath AI
                        </h1>

                        <p className="mt-6 text-lg leading-relaxed font-medium text-muted-foreground">
                            Dengan menggunakan SkillPath AI, pengguna menyetujui
                            penggunaan layanan sesuai ketentuan berikut.
                        </p>
                    </div>

                    <div className="mt-12 grid gap-5">
                        {sections.map((section) => (
                            <section
                                key={section.title}
                                className="neo-card p-6 sm:p-7"
                            >
                                <h2 className="text-xl font-black tracking-tight">
                                    {section.title}
                                </h2>

                                <p className="mt-3 leading-relaxed font-medium text-muted-foreground">
                                    {section.content}
                                </p>
                            </section>
                        ))}
                    </div>
                </div>
            </main>
        </>
    );
}
