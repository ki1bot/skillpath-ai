import { Book, BookOpen } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { useSidebar } from '@/components/ui/sidebar';
import { cn } from '@/lib/utils';

export function SidebarBookToggle({ className }: { className?: string }) {
    const { isMobile, openMobile, state, toggleSidebar } = useSidebar();

    const isOpen = isMobile ? openMobile : state === 'expanded';

    const label = isOpen ? 'Tutup menu samping' : 'Buka menu samping';

    return (
        <Button
            type="button"
            variant="secondary"
            size="icon"
            onClick={toggleSidebar}
            aria-label={label}
            title={label}
            className={cn(
                'group relative size-10 overflow-hidden rounded-[11px] border-2 border-[#171717] bg-secondary text-[#171717] shadow-[3px_3px_0_var(--neo-shadow-color)] transition-[transform,box-shadow,background-color] duration-200 hover:-translate-y-[1px] hover:bg-secondary active:translate-x-[2px] active:translate-y-[2px] active:shadow-none',
                className,
            )}
        >
            <span
                className="relative block size-5 [perspective:120px]"
                aria-hidden="true"
            >
                <BookOpen
                    className={cn(
                        'absolute inset-0 size-5 transition-all duration-300 ease-out',
                        isOpen
                            ? 'scale-100 rotate-0 opacity-100'
                            : 'scale-75 -rotate-12 opacity-0',
                    )}
                />

                <Book
                    className={cn(
                        'absolute inset-0 size-5 transition-all duration-300 ease-out',
                        isOpen
                            ? 'scale-75 rotate-12 opacity-0'
                            : 'scale-100 rotate-0 opacity-100',
                    )}
                />
            </span>

            <span className="sr-only">{label}</span>
        </Button>
    );
}
