"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.shouldServe = exports.prepareCache = exports.build = exports.version = void 0;
const path_1 = __importDefault(require("path"));
const build_utils_1 = require("@vercel/build-utils");
Object.defineProperty(exports, "shouldServe", { enumerable: true, get: function () { return build_utils_1.shouldServe; } });
const utils_1 = require("./utils");
const COMPOSER_FILE = process.env.COMPOSER || 'composer.json';
// ###########################
// EXPORTS
// ###########################
exports.version = 3;
const build = async ({ files, entrypoint, workPath, config = {}, meta = {}, }) => {
    // Check if now dev mode is used
    if (meta.isDev) {
        console.log(`
      🐘 vercel dev is not supported right now.
      Please use PHP built-in development server.

      php -S localhost:8000 api/index.php
    `);
        process.exit(255);
    }
    console.log('🐘 Downloading user files');
    // Collect user provided files
    const userFiles = await (0, build_utils_1.download)(files, workPath, meta);
    console.log('🐘 Downloading PHP runtime files');
    // Collect runtime files containing PHP bins and libs
    const runtimeFiles = {
        // Append PHP files (bins + shared object)
        ...await (0, utils_1.getPhpFiles)(),
        // Append launcher files (builtin server, common helpers)
        ...(0, utils_1.getLauncherFiles)(),
    };
    // If composer.json is provided try to
    // - install deps
    // - run composer scripts
    if (userFiles[COMPOSER_FILE]) {
        // Install dependencies (vendor is collected bellow, see harvestedFiles)
        await (0, utils_1.runComposerInstall)(workPath);
        // Run composer scripts (created files are collected bellow, , see harvestedFiles)
        await (0, utils_1.runComposerScripts)(userFiles[COMPOSER_FILE], workPath);
    }
    // Append PHP directives into php.ini
    if (userFiles['api/php.ini']) {
        const phpini = await (0, utils_1.modifyPhpIni)(userFiles, runtimeFiles);
        if (phpini) {
            runtimeFiles['php/php.ini'] = phpini;
        }
    }
    // Collect user files, files creating during build (composer vendor)
    // and other files and prefix them with "user" (/var/task/user folder).
    const harverstedFiles = (0, build_utils_1.rename)(await (0, build_utils_1.glob)('**', {
        cwd: workPath,
        ignore: [
            '.vercel/**',
            ...((config === null || config === void 0 ? void 0 : config.excludeFiles)
                ? Array.isArray(config.excludeFiles)
                    ? config.excludeFiles
                    : [config.excludeFiles]
                : [
                    'node_modules/**',
                    'now.json',
                    '.nowignore',
                    'vercel.json',
                    '.vercelignore',
                ]),
        ],
    }), name => path_1.default.join('user', name));
    // Show some debug notes during build
    if (process.env.NOW_PHP_DEBUG === '1') {
        console.log('🐘 Entrypoint:', entrypoint);
        console.log('🐘 Config:', config);
        console.log('🐘 Work path:', workPath);
        console.log('🐘 Meta:', meta);
        console.log('🐘 User files:', Object.keys(harverstedFiles));
        console.log('🐘 Runtime files:', Object.keys(runtimeFiles));
        console.log('🐘 PHP: php.ini', await (0, utils_1.readRuntimeFile)(runtimeFiles['php/php.ini']));
    }
    console.log('🐘 Creating lambda');
    const nodeVersion = await (0, build_utils_1.getNodeVersion)(workPath);
    const lambda = new build_utils_1.Lambda({
        files: {
            // Located at /var/task/user
            ...harverstedFiles,
            // Located at /var/task/php (php bins + ini + modules)
            // Located at /var/task/lib (shared libs)
            ...runtimeFiles
        },
        handler: 'launcher.launcher',
        runtime: nodeVersion.runtime,
        environment: {
            NOW_ENTRYPOINT: entrypoint,
            NOW_PHP_DEV: meta.isDev ? '1' : '0'
        },
    });
    return { output: lambda };
};
exports.build = build;
const prepareCache = async ({ workPath }) => {
    return {
        // Composer
        ...(await (0, build_utils_1.glob)('vendor/**', workPath)),
        ...(await (0, build_utils_1.glob)('composer.lock', workPath)),
        // NPM
        ...(await (0, build_utils_1.glob)('node_modules/**', workPath)),
        ...(await (0, build_utils_1.glob)('package-lock.json', workPath)),
        ...(await (0, build_utils_1.glob)('yarn.lock', workPath)),
    };
};
exports.prepareCache = prepareCache;
