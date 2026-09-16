import { Head, Link } from '@inertiajs/react';
import PublicBackLink from '@/components/public-back-link';
import { Button } from '@/components/ui/button';

export default function DataDeletion() {
    return (
        <>
            <Head title="Penghapusan Data" />

            <main className="neo-page py-14 lg:py-20">
                <div className="mx-auto max-w-4xl">
                    <PublicBackLink />

                    <div className="mt-8">
                        <span className="neo-label">Data pengguna</span>

                        <h1 className="neo-heading mt-6 text-5xl sm:text-6xl">
                            Penghapusan Data SkillPath AI
                        </h1>

                        <p className="mt-6 text-lg leading-relaxed font-medium text-muted-foreground">
                            Pengguna SkillPath AI dapat menghapus akun dan data
                            yang terikat pada akun melalui pengaturan profil.
                        </p>
                    </div>

                    <div className="mt-12 grid gap-5">
                        <section className="neo-card p-6 sm:p-7">
                            <p className="text-xs font-black tracking-[0.16em] text-muted-foreground uppercase">
                                Langkah 1
                            </p>

                            <h2 className="mt-2 text-xl font-black">
                                Masuk ke akun SkillPath AI
                            </h2>

                            <p className="mt-3 leading-relaxed font-medium text-muted-foreground">
                                Gunakan email dan kata sandi, Google, atau
                                Facebook yang sudah terhubung ke akun Anda.
                            </p>
                        </section>

                        <section className="neo-card p-6 sm:p-7">
                            <p className="text-xs font-black tracking-[0.16em] text-muted-foreground uppercase">
                                Langkah 2
                            </p>

                            <h2 className="mt-2 text-xl font-black">
                                Buka Pengaturan Profil
                            </h2>

                            <p className="mt-3 leading-relaxed font-medium text-muted-foreground">
                                Masuk ke halaman Pengaturan Profil pada akun
                                SkillPath AI Anda.
                            </p>
                        </section>

                        <section className="neo-card p-6 sm:p-7">
                            <p className="text-xs font-black tracking-[0.16em] text-muted-foreground uppercase">
                                Langkah 3
                            </p>

                            <h2 className="mt-2 text-xl font-black">
                                Pilih penghapusan akun
                            </h2>

                            <p className="mt-3 leading-relaxed font-medium text-muted-foreground">
                                Gunakan fitur Hapus Akun dan selesaikan
                                konfirmasi keamanan yang diminta oleh sistem.
                            </p>
                        </section>

                        <section className="rounded-[16px] border-2 border-[#171717] bg-secondary p-7 text-[#171717] shadow-[5px_5px_0_#171717]">
                            <h2 className="text-2xl font-black">
                                Apa yang terjadi setelah akun dihapus?
                            </h2>

                            <p className="mt-3 leading-relaxed font-semibold">
                                Akun pengguna akan dihapus dari SkillPath AI.
                                Koneksi Google atau Facebook yang terkait dengan
                                akun tersebut juga dihapus melalui relasi
                                database akun pengguna.
                            </p>
                        </section>
                    </div>

                    <div className="mt-8">
                        <Button asChild>
                            <Link href="/login">Masuk ke SkillPath AI</Link>
                        </Button>
                    </div>
                </div>
            </main>
        </>
    );
}
