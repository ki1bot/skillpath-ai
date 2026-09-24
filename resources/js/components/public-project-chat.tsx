import { MessageCircle, SendHorizontal, X } from 'lucide-react';
import { useEffect, useMemo, useRef, useState } from 'react';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';

type ChatRole = 'user' | 'assistant';

type ChatMessage = {
    id: string;
    role: ChatRole;
    content: string;
};

type ChatResponse = {
    message?: string;
    blocked?: boolean;
    errors?: Record<string, string[]>;
};

const welcomeMessage: ChatMessage = {
    id: 'welcome',
    role: 'assistant',
    content:
        'Halo! Ada yang ingin kamu tanyakan tentang SkillPath? Aku bisa bantu menjelaskan fitur, cara menggunakan website, dan informasi umum tentang project ini.',
};

const quickQuestions = [
    'SkillPath itu apa?',
    'Bagaimana cara mulai menggunakan SkillPath?',
    'Teknologi apa yang digunakan?',
];

function csrfToken(): string {
    if (typeof document === 'undefined') {
        return '';
    }

    const tokenCookie = document.cookie
        .split('; ')
        .find((cookie) => cookie.startsWith('XSRF-TOKEN='));

    if (!tokenCookie) {
        return '';
    }

    const value = tokenCookie.slice('XSRF-TOKEN='.length);

    try {
        return decodeURIComponent(value);
    } catch {
        return value;
    }
}

export default function PublicProjectChat() {
    const [open, setOpen] = useState(false);

    const [messages, setMessages] = useState<ChatMessage[]>([welcomeMessage]);

    const [input, setInput] = useState('');
    const [sending, setSending] = useState(false);

    const scrollRef = useRef<HTMLDivElement>(null);

    const inputRef = useRef<HTMLTextAreaElement>(null);

    const visibleQuickQuestions = useMemo(
        () => messages.length === 1 && !sending,
        [messages.length, sending],
    );

    useEffect(() => {
        if (!open) {
            return;
        }

        requestAnimationFrame(() => {
            scrollRef.current?.scrollTo({
                top: scrollRef.current.scrollHeight,
                behavior: 'smooth',
            });
        });
    }, [messages, open, sending]);

    useEffect(() => {
        if (!open) {
            return;
        }

        const isDesktop = window.matchMedia('(min-width: 640px)').matches;

        if (!isDesktop) {
            return;
        }

        const timer = window.setTimeout(() => {
            inputRef.current?.focus();
        }, 120);

        return () => window.clearTimeout(timer);
    }, [open]);

    useEffect(() => {
        if (!open) {
            return;
        }

        const handleKeyDown = (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                setOpen(false);
            }
        };

        window.addEventListener('keydown', handleKeyDown);

        return () => {
            window.removeEventListener('keydown', handleKeyDown);
        };
    }, [open]);

    useEffect(() => {
        if (!open) {
            return;
        }

        const isMobile = window.matchMedia('(max-width: 639px)').matches;

        if (!isMobile) {
            return;
        }

        const previousOverflow = document.body.style.overflow;

        document.body.style.overflow = 'hidden';

        return () => {
            document.body.style.overflow = previousOverflow;
        };
    }, [open]);

    const appendAssistantMessage = (content: string) => {
        setMessages((current) => [
            ...current,
            {
                id: `assistant-${Date.now()}-${Math.random()}`,
                role: 'assistant',
                content,
            },
        ]);
    };

    const sendMessage = async (preset?: string) => {
        const message = (preset ?? input).trim();

        if (message.length < 2 || sending) {
            return;
        }

        const history = messages
            .filter((item) => item.id !== 'welcome')
            .slice(-8)
            .map((item) => ({
                role: item.role,
                content: item.content,
            }));

        setMessages((current) => [
            ...current,
            {
                id: `user-${Date.now()}-${Math.random()}`,
                role: 'user',
                content: message,
            },
        ]);

        setInput('');
        setSending(true);

        try {
            const token = csrfToken();

            const response = await fetch('/bantuan/chat', {
                method: 'POST',

                credentials: 'same-origin',

                headers: {
                    Accept: 'application/json',

                    'Content-Type': 'application/json',

                    'X-Requested-With': 'XMLHttpRequest',

                    ...(token
                        ? {
                              'X-XSRF-TOKEN': token,
                          }
                        : {}),
                },

                body: JSON.stringify({
                    message,
                    history,
                }),
            });

            const data = (await response
                .json()
                .catch(() => ({}))) as ChatResponse;

            if (response.status === 429) {
                appendAssistantMessage(
                    'Pesannya terlalu cepat masuk. Tunggu sebentar, lalu coba kirim lagi.',
                );

                return;
            }

            if (response.status === 422) {
                const firstError = Object.values(data.errors ?? {})
                    .flat()
                    .find(Boolean);

                appendAssistantMessage(
                    firstError ??
                        'Pesan belum bisa dikirim. Coba tulis pertanyaan yang lebih singkat.',
                );

                return;
            }

            if (!response.ok) {
                appendAssistantMessage(
                    data.message ??
                        'Maaf, layanan bantuan sedang belum bisa merespons. Coba lagi beberapa saat lagi.',
                );

                return;
            }

            if (typeof data.message !== 'string' || !data.message.trim()) {
                appendAssistantMessage(
                    'Maaf, responsnya belum tersedia. Coba kirim pertanyaan sekali lagi.',
                );

                return;
            }

            appendAssistantMessage(data.message.trim());
        } catch {
            appendAssistantMessage(
                'Koneksi ke layanan bantuan terputus. Periksa koneksi internetmu lalu coba lagi.',
            );
        } finally {
            setSending(false);
        }
    };

    return (
        <>
            {open && (
                <button
                    type="button"
                    className="fixed inset-0 z-40 bg-black/20 backdrop-blur-[1px] sm:hidden"
                    onClick={() => setOpen(false)}
                    aria-label="Tutup bantuan SkillPath"
                />
            )}

            <div className="fixed inset-x-3 bottom-[max(0.75rem,env(safe-area-inset-bottom))] z-50 flex justify-end sm:right-6 sm:bottom-6 sm:left-auto">
                {open ? (
                    <section
                        className="flex h-[calc(100dvh-5.5rem)] max-h-[44rem] w-full flex-col overflow-hidden rounded-[12px] border-2 border-foreground bg-card shadow-[4px_4px_0_var(--neo-shadow-color)] sm:h-[min(38rem,calc(100dvh-7rem))] sm:w-[25rem] sm:rounded-[14px] sm:shadow-[6px_6px_0_var(--neo-shadow-color)]"
                        role="dialog"
                        aria-label="Bantuan SkillPath"
                    >
                        <header className="flex shrink-0 items-center justify-between gap-3 border-b-2 border-[#171717] bg-[var(--neo-blue)] px-3 py-2.5 text-[#171717] sm:px-4 sm:py-3">
                            <div className="min-w-0">
                                <div className="flex items-center gap-2">
                                    <span className="flex size-8 shrink-0 items-center justify-center rounded-[8px] border-2 border-[#171717] bg-[#fffdf8] shadow-[2px_2px_0_#171717]">
                                        <MessageCircle className="size-4" />
                                    </span>

                                    <div className="min-w-0">
                                        <p className="truncate text-sm font-black">
                                            Bantuan SkillPath
                                        </p>

                                        <p className="truncate text-[10px] font-bold text-[#171717]/65 sm:text-[11px]">
                                            Tanya seputar website
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <Button
                                type="button"
                                variant="outline"
                                size="icon-sm"
                                className="shrink-0 border-[#171717] bg-[#fffdf8] text-[#171717] hover:bg-[#f3efe6] hover:text-[#171717]"
                                onClick={() => setOpen(false)}
                                aria-label="Tutup bantuan"
                            >
                                <X />
                            </Button>
                        </header>

                        <div
                            ref={scrollRef}
                            className="min-h-0 flex-1 space-y-3 overflow-y-auto overscroll-contain bg-background/45 p-3 sm:p-4"
                            aria-live="polite"
                        >
                            {messages.map((message) => (
                                <div
                                    key={message.id}
                                    className={`flex ${
                                        message.role === 'user'
                                            ? 'justify-end'
                                            : 'justify-start'
                                    }`}
                                >
                                    <div
                                        className={`max-w-[92%] rounded-[10px] border-2 border-foreground px-3 py-2.5 text-[13px] leading-5 font-medium break-words whitespace-pre-wrap shadow-[2px_2px_0_var(--neo-shadow-color)] sm:max-w-[88%] sm:rounded-[11px] sm:px-3.5 sm:text-sm sm:leading-6 ${
                                            message.role === 'user'
                                                ? 'bg-[var(--neo-lime)] text-[#171717]'
                                                : 'bg-card text-card-foreground'
                                        }`}
                                    >
                                        {message.content}
                                    </div>
                                </div>
                            ))}

                            {visibleQuickQuestions && (
                                <div className="grid gap-2 pt-1">
                                    {quickQuestions.map((question) => (
                                        <button
                                            key={question}
                                            type="button"
                                            className="min-h-11 rounded-[9px] border-2 border-foreground bg-card px-3 py-2.5 text-left text-xs leading-5 font-bold transition-transform active:translate-x-[1px] active:translate-y-[1px] active:shadow-none sm:min-h-0 sm:py-2 sm:hover:-translate-y-0.5"
                                            onClick={() =>
                                                void sendMessage(question)
                                            }
                                        >
                                            {question}
                                        </button>
                                    ))}
                                </div>
                            )}

                            {sending && (
                                <div className="flex justify-start">
                                    <div className="rounded-[10px] border-2 border-foreground bg-card px-3.5 py-2.5 text-sm font-bold shadow-[2px_2px_0_var(--neo-shadow-color)] sm:rounded-[11px]">
                                        <span className="inline-flex items-center gap-1.5">
                                            <span className="size-1.5 animate-pulse rounded-full bg-foreground" />

                                            <span className="size-1.5 animate-pulse rounded-full bg-foreground [animation-delay:120ms]" />

                                            <span className="size-1.5 animate-pulse rounded-full bg-foreground [animation-delay:240ms]" />
                                        </span>
                                    </div>
                                </div>
                            )}
                        </div>

                        <div className="shrink-0 border-t-2 border-foreground bg-card p-2.5 sm:p-3">
                            <div className="flex items-end gap-2">
                                <Textarea
                                    ref={inputRef}
                                    value={input}
                                    onChange={(event) =>
                                        setInput(event.target.value)
                                    }
                                    onKeyDown={(event) => {
                                        if (
                                            event.key === 'Enter' &&
                                            !event.shiftKey
                                        ) {
                                            event.preventDefault();

                                            void sendMessage();
                                        }
                                    }}
                                    placeholder="Tulis pertanyaan tentang SkillPath..."
                                    className="max-h-28 min-h-11 resize-none px-3 py-2.5 text-base sm:text-sm"
                                    maxLength={600}
                                    disabled={sending}
                                    aria-label="Pesan untuk bantuan SkillPath"
                                />

                                <Button
                                    type="button"
                                    size="icon"
                                    className="size-11 shrink-0"
                                    onClick={() => void sendMessage()}
                                    disabled={
                                        sending || input.trim().length < 2
                                    }
                                    aria-label="Kirim pesan"
                                >
                                    <SendHorizontal />
                                </Button>
                            </div>
                        </div>
                    </section>
                ) : (
                    <button
                        type="button"
                        onClick={() => setOpen(true)}
                        className="neo-interactive flex size-12 items-center justify-center rounded-full border-2 border-foreground bg-[var(--neo-blue)] text-sm font-black text-[#171717] shadow-[4px_4px_0_var(--neo-shadow-color)] sm:size-auto sm:gap-2 sm:px-4 sm:py-3"
                        aria-label="Buka bantuan SkillPath"
                    >
                        <MessageCircle className="size-5" />

                        <span className="hidden sm:inline">
                            Tanya SkillPath
                        </span>
                    </button>
                )}
            </div>
        </>
    );
}
