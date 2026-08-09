import { Head } from '@inertiajs/react';
import { Check, Palette, Sparkles } from 'lucide-react';
import AppearanceTabs from '@/components/appearance-tabs';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { edit as editAppearance } from '@/routes/appearance';

export default function Appearance() {
    return (
        <>
            <Head title="Pengaturan tampilan" />

            <h1 className="sr-only">Pengaturan tampilan</h1>

            <div className="space-y-7">
                <Heading
                    variant="small"
                    title="Pengaturan tampilan"
                    description="Pilih tema yang paling nyaman digunakan. Mode sistem akan mengikuti pengaturan perangkat Anda."
                />

                <AppearanceTabs />

                <div className="rounded-[14px] border-2 border-foreground bg-background p-5 sm:p-6">
                    <div className="flex items-center gap-3">
                        <span className="flex size-10 items-center justify-center rounded-[10px] border-2 border-[#171717] bg-[var(--neo-blue)] text-[#171717]">
                            <Palette className="size-5" />
                        </span>

                        <div>
                            <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                Pratinjau
                            </p>

                            <h2 className="text-lg font-black">
                                Neo-brutalism SkillPath AI
                            </h2>
                        </div>
                    </div>

                    <div className="mt-6 grid gap-4 sm:grid-cols-2">
                        <div className="rounded-[13px] border-2 border-foreground bg-card p-5 shadow-[4px_4px_0_var(--neo-shadow-color)]">
                            <span className="neo-label">
                                <Sparkles className="size-3" />
                                Contoh kartu
                            </span>

                            <h3 className="mt-5 text-2xl font-black">
                                Tampilan tetap konsisten.
                            </h3>

                            <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                                Border tegas, bayangan pendek, serta warna aksen
                                digunakan seperlunya.
                            </p>
                        </div>

                        <div className="rounded-[13px] border-2 border-[#171717] bg-[var(--neo-lime)] p-5 text-[#171717] shadow-[4px_4px_0_var(--neo-shadow-color)]">
                            <div className="flex size-9 items-center justify-center rounded-full border-2 border-[#171717] bg-[#fffdf7]">
                                <Check className="size-4" />
                            </div>

                            <h3 className="mt-5 text-2xl font-black">
                                Kontras tetap jelas.
                            </h3>

                            <p className="mt-2 text-sm leading-6 font-semibold">
                                Warna aksen selalu menggunakan teks yang tetap
                                terbaca pada mode terang maupun gelap.
                            </p>

                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                className="mt-5 border-[#171717] bg-[#fffdf7] text-[#171717]"
                            >
                                Contoh tombol
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

Appearance.layout = {
    breadcrumbs: [
        {
            title: 'Pengaturan tampilan',
            href: editAppearance(),
        },
    ],
};
