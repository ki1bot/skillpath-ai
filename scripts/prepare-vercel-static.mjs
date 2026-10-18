import { cp, mkdir, rm } from 'node:fs/promises';
import { relative, resolve, sep } from 'node:path';

const source = resolve('public');
const destination = resolve('vercel-static');

await rm(destination, {
    recursive: true,
    force: true,
});

await mkdir(destination, {
    recursive: true,
});

await cp(source, destination, {
    recursive: true,
    dereference: false,
    filter: (entry) => {
        const relativePath = relative(source, entry);

        if (!relativePath) {
            return true;
        }

        const segments = relativePath.split(sep);

        if (segments.includes('storage')) {
            return false;
        }

        if (segments.includes('hot')) {
            return false;
        }

        if (segments.some((segment) => segment.startsWith('.'))) {
            return false;
        }

        if (entry.toLowerCase().endsWith('.php')) {
            return false;
        }

        return true;
    },
});
