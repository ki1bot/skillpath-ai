import { SessionKeepAlive } from '@/components/session-keep-alive';
import { useFlashToast } from '@/hooks/use-flash-toast';

export default function RootLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    useFlashToast();

    return (
        <>
            <SessionKeepAlive />
            {children}
        </>
    );
}
