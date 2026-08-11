import AppLogoIcon from '@/components/app-logo-icon';

export default function AppLogo() {
    return (
        <>
            <div className="flex aspect-square size-9 shrink-0 items-center justify-center rounded-[9px] border-2 border-[#171717] bg-secondary text-[#171717] shadow-[2px_2px_0_var(--neo-shadow-color)]">
                <AppLogoIcon className="size-5" />
            </div>

            <div className="ml-1 grid min-w-0 flex-1 text-left group-data-[collapsible=icon]:hidden">
                <span className="truncate text-sm leading-tight font-extrabold tracking-tight">
                    SkillPath AI
                </span>

                <span className="truncate text-[10px] font-bold tracking-[0.08em] text-muted-foreground uppercase">
                    Jalur belajar personal
                </span>
            </div>
        </>
    );
}
