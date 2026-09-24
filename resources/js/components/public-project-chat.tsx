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

        const timer = window.setTimeout(() => {
            inputRef.current?.focus();
        }, 120);

        return () => window.clearTimeout(timer);
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
        <div className="fixed right-4 bottom-4 z-50 sm:right-6 sm:bottom-6">
            {open ? (
                <section
                    className="flex h-[min(38rem,calc(100vh-7rem))] w-[min(25rem,calc(100vw-2rem))] flex-col overflow-hidden rounded-[14px] border-2 border-foreground bg-card shadow-[6px_6px_0_var(--neo-shadow-color)]"
                    aria-label="Bantuan SkillPath"
                >
                    <header className="flex items-center justify-between gap-3 border-b-2 border-[#171717] bg-[var(--neo-blue)] px-4 py-3 text-[#171717]">
                        <div className="min-w-0">
                            <div className="flex items-center gap-2">
                                <span className="flex size-8 shrink-0 items-center justify-center rounded-[8px] border-2 border-[#171717] bg-[#fffdf8] shadow-[2px_2px_0_#171717]">
                                    <MessageCircle className="size-4" />
                                </span>

                                <div className="min-w-0">
                                    <p className="truncate text-sm font-black">
                                        Bantuan SkillPath
                                    </p>

                                    <p className="text-[11px] font-bold text-[#171717]/65">
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
                        className="min-h-0 flex-1 space-y-3 overflow-y-auto bg-background/45 p-4"
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
                                    className={`max-w-[88%] rounded-[11px] border-2 border-foreground px-3.5 py-2.5 text-sm leading-6 font-medium whitespace-pre-wrap shadow-[2px_2px_0_var(--neo-shadow-color)] ${
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
                                        className="rounded-[9px] border-2 border-foreground bg-card px-3 py-2 text-left text-xs leading-5 font-bold transition-transform hover:-translate-y-0.5"
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
                                <div className="rounded-[11px] border-2 border-foreground bg-card px-3.5 py-2.5 text-sm font-bold shadow-[2px_2px_0_var(--neo-shadow-color)]">
                                    <span className="inline-flex items-center gap-1.5">
                                        <span className="size-1.5 animate-pulse rounded-full bg-foreground" />

                                        <span className="size-1.5 animate-pulse rounded-full bg-foreground [animation-delay:120ms]" />

                                        <span className="size-1.5 animate-pulse rounded-full bg-foreground [animation-delay:240ms]" />
                                    </span>
                                </div>
                            </div>
                        )}
                    </div>

                    <div className="border-t-2 border-foreground bg-card p-3">
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
                                className="max-h-28 min-h-11 resize-none"
                                maxLength={600}
                                disabled={sending}
                                aria-label="Pesan untuk bantuan SkillPath"
                            />

                            <Button
                                type="button"
                                size="icon"
                                className="shrink-0"
                                onClick={() => void sendMessage()}
                                disabled={sending || input.trim().length < 2}
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
                    className="neo-interactive flex items-center gap-2 rounded-full border-2 border-foreground bg-[var(--neo-blue)] px-4 py-3 text-sm font-black text-[#171717] shadow-[4px_4px_0_var(--neo-shadow-color)]"
                    aria-label="Buka bantuan SkillPath"
                >
                    <MessageCircle className="size-5" />

                    <span className="hidden sm:inline">Tanya SkillPath</span>
                </button>
            )}
        </div>
    );
}
