import AppLogoIcon from '@/components/app-logo-icon';

export default function AppLogo() {
    return (
        <>
            <div className="flex aspect-square size-9 items-center justify-center rounded-xl border-2 border-foreground bg-secondary text-[#171717] shadow-[3px_3px_0_var(--foreground)]">
                <AppLogoIcon className="size-5" />
            </div>
            <div className="ml-1 grid flex-1 text-left">
                <span className="truncate text-sm leading-tight font-black tracking-tight">
                    SkillPath AI
                </span>
                <span className="truncate text-[10px] font-semibold tracking-[0.16em] text-muted-foreground uppercase">
                    Learn with evidence
                </span>
            </div>
        </>
    );
}
