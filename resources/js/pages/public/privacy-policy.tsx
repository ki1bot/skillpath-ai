import { Head } from '@inertiajs/react';

const sections = [
    {
        title: 'Data yang kami proses',
        content:
            'SkillPath AI dapat memproses informasi akun seperti nama dan alamat email, jurusan atau program studi, hasil assessment, kemampuan, jalur belajar, progres pembelajaran, evaluasi, proyek, feedback, serta informasi autentikasi yang diperlukan untuk mengakses akun.',
    },
    {
        title: 'Login dengan Google dan Facebook',
        content:
            'Jika Anda menggunakan Google atau Facebook untuk masuk atau menautkan akun, SkillPath AI menerima identitas akun dari penyedia tersebut, termasuk identifier pengguna dan informasi dasar yang Anda izinkan seperti nama dan alamat email. SkillPath AI tidak menerima kata sandi Google atau Facebook Anda.',
    },
    {
        title: 'Tujuan penggunaan data',
        content:
            'Data digunakan untuk menyediakan autentikasi, menyimpan progres, menjalankan assessment, menghitung pemetaan kemampuan, menyusun jalur belajar, menampilkan rekomendasi, menyediakan fitur evaluasi, mengelola akun, dan menjaga keamanan layanan.',
    },
    {
        title: 'Penyimpanan dan keamanan',
        content:
            'SkillPath AI menggunakan mekanisme autentikasi, pembatasan akses, validasi, dan penyimpanan database untuk membantu melindungi data pengguna. Akses administratif dibatasi sesuai peran pengguna di dalam sistem.',
    },
    {
        title: 'Layanan pihak ketiga',
        content:
            'Beberapa fungsi SkillPath AI bergantung pada layanan pihak ketiga, termasuk penyedia autentikasi, pengiriman email, hosting, database, serta layanan AI. Data hanya dikirimkan sesuai kebutuhan fitur yang sedang digunakan.',
    },
    {
        title: 'Penghapusan data',
        content:
            'Pengguna dapat menghapus akun melalui Pengaturan Profil. Ketika akun dihapus, data akun yang terikat pada pengguna tersebut akan diproses sesuai struktur dan relasi data aplikasi. Petunjuk lebih lengkap tersedia pada halaman Penghapusan Data.',
    },
    {
        title: 'Perubahan kebijakan',
        content:
            'Kebijakan ini dapat diperbarui apabila fitur, cara pemrosesan data, atau layanan yang digunakan SkillPath AI berubah. Versi terbaru selalu ditampilkan pada halaman ini.',
    },
];

export default function PrivacyPolicy() {
    return (
        <>
            <Head title="Kebijakan Privasi" />

            <main className="neo-page py-14 lg:py-20">
                <div className="mx-auto max-w-4xl">
                    <span className="neo-label">Privasi</span>

                    <h1 className="neo-heading mt-6 text-5xl sm:text-6xl">
                        Kebijakan Privasi SkillPath AI
                    </h1>

                    <p className="mt-6 text-lg leading-relaxed font-medium text-muted-foreground">
                        Halaman ini menjelaskan bagaimana SkillPath AI memproses
                        informasi yang diperlukan untuk menyediakan akun,
                        assessment, jalur belajar, progres, dan fitur lainnya.
                    </p>

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
