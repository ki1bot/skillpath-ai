import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/hooks/use-initials';
import type { User } from '@/types';

export function UserInfo({
    user,
    showEmail = false,
}: {
    user: User;
    showEmail?: boolean;
}) {
    const getInitials = useInitials();

    return (
        <>
            <Avatar className="size-9 shrink-0 overflow-hidden rounded-full border-2 border-current/20">
                <AvatarImage src={user.avatar} alt={user.name} />

                <AvatarFallback className="rounded-full bg-secondary font-black text-[#171717]">
                    {getInitials(user.name)}
                </AvatarFallback>
            </Avatar>

            <div className="grid min-w-0 flex-1 text-left leading-tight">
                <span className="truncate text-sm font-extrabold">
                    {user.name}
                </span>

                {showEmail && (
                    <span className="mt-0.5 truncate text-xs font-medium text-muted-foreground">
                        {user.email}
                    </span>
                )}
            </div>
        </>
    );
}
