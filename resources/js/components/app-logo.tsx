import AppLogoIcon from '@/components/app-logo-icon';

export default function AppLogo() {
    return (
        <>
            <div className="flex aspect-square size-10 items-center justify-center rounded-[11px] border-2 border-[#171717] bg-secondary text-[#171717] shadow-[3px_3px_0_var(--neo-shadow-color)]">
                <AppLogoIcon className="size-6" />
            </div>

            <div className="ml-1 grid min-w-0 flex-1 text-left">
                <span className="truncate text-sm leading-tight font-black tracking-tight">
                    SkillPath AI
                </span>

                <span className="truncate text-[9px] font-black tracking-[0.15em] text-muted-foreground uppercase">
                    Belajar dengan bukti
                </span>
            </div>
        </>
    );
}
