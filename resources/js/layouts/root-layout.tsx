import { IdleSessionGuard } from '@/components/idle-session-guard';
import { useFlashToast } from '@/hooks/use-flash-toast';

export default function RootLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    useFlashToast();

    return (
        <>
            <IdleSessionGuard />
            {children}
        </>
    );
}
