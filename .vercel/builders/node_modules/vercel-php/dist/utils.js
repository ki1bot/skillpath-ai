"use strict";
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || (function () {
    var ownKeys = function(o) {
        ownKeys = Object.getOwnPropertyNames || function (o) {
            var ar = [];
            for (var k in o) if (Object.prototype.hasOwnProperty.call(o, k)) ar[ar.length] = k;
            return ar;
        };
        return ownKeys(o);
    };
    return function (mod) {
        if (mod && mod.__esModule) return mod;
        var result = {};
        if (mod != null) for (var k = ownKeys(mod), i = 0; i < k.length; i++) if (k[i] !== "default") __createBinding(result, mod, k[i]);
        __setModuleDefault(result, mod);
        return result;
    };
})();
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.getPhpFiles = getPhpFiles;
exports.getLauncherFiles = getLauncherFiles;
exports.modifyPhpIni = modifyPhpIni;
exports.runComposerInstall = runComposerInstall;
exports.runComposerScripts = runComposerScripts;
exports.ensureLocalPhp = ensureLocalPhp;
exports.readRuntimeFile = readRuntimeFile;
const path_1 = __importDefault(require("path"));
const child_process_1 = require("child_process");
const build_utils_1 = require("@vercel/build-utils");
const libphp = __importStar(require("@libphp/almalinux-9-v85"));
const PHP_PKG = path_1.default.dirname(require.resolve('@libphp/almalinux-9-v85/package.json'));
const PHP_BIN_DIR = path_1.default.join(PHP_PKG, "native/php");
const PHP_MODULES_DIR = path_1.default.join(PHP_BIN_DIR, "modules");
const PHP_LIB_DIR = path_1.default.join(PHP_PKG, "native/lib");
const COMPOSER_BIN = path_1.default.join(PHP_BIN_DIR, "composer");
async function getPhpFiles() {
    const files = await libphp.getFiles();
    // Drop CGI + FPM from libphp, it's not needed for our case
    delete files['php/php-cgi'];
    delete files['php/php-fpm'];
    delete files['php/php-fpm.ini'];
    const runtimeFiles = {};
    // Map from @libphp to Vercel's File objects
    for (const [filename, filepath] of Object.entries(files)) {
        runtimeFiles[filename] = new build_utils_1.FileFsRef({
            fsPath: filepath
        });
    }
    // Set some bins executable
    runtimeFiles['php/php'].mode = 33261; // 0755;
    runtimeFiles['php/composer'].mode = 33261; // 0755;
    return runtimeFiles;
}
function getLauncherFiles() {
    const files = {
        'helpers.js': new build_utils_1.FileFsRef({
            fsPath: path_1.default.join(__dirname, 'launchers/helpers.js'),
        })
    };
    files['launcher.js'] = new build_utils_1.FileFsRef({
        fsPath: path_1.default.join(__dirname, 'launchers/builtin.js'),
    });
    return files;
}
async function modifyPhpIni(userFiles, runtimeFiles) {
    // Validate user files contains php.ini
    if (!userFiles['api/php.ini'])
        return;
    // Validate runtime contains php.ini
    if (!runtimeFiles['php/php.ini'])
        return;
    const phpiniBlob = await build_utils_1.FileBlob.fromStream({
        stream: runtimeFiles['php/php.ini'].toStream(),
    });
    const userPhpiniBlob = await build_utils_1.FileBlob.fromStream({
        stream: userFiles['api/php.ini'].toStream(),
    });
    return new build_utils_1.FileBlob({
        data: phpiniBlob.data.toString()
            .concat("; [User]\n")
            .concat(userPhpiniBlob.data.toString())
    });
}
async function runComposerInstall(workPath) {
    console.log('🐘 Installing Composer dependencies [START]');
    // @todo PHP_COMPOSER_INSTALL env
    await runPhp([
        COMPOSER_BIN,
        'install',
        '--profile',
        '--no-dev',
        '--no-interaction',
        '--no-scripts',
        '--ignore-platform-reqs',
        '--no-progress'
    ], {
        stdio: 'inherit',
        cwd: workPath
    });
    console.log('🐘 Installing Composer dependencies [DONE]');
}
async function runComposerScripts(composerFile, workPath) {
    var _a;
    let composer;
    try {
        composer = JSON.parse(await readRuntimeFile(composerFile));
    }
    catch (e) {
        console.error('🐘 Composer file is not valid JSON');
        console.error(e);
        return;
    }
    if ((_a = composer === null || composer === void 0 ? void 0 : composer.scripts) === null || _a === void 0 ? void 0 : _a.vercel) {
        console.log('🐘 Running composer scripts [START]');
        await runPhp([COMPOSER_BIN, 'run', 'vercel'], {
            stdio: 'inherit',
            cwd: workPath
        });
        console.log('🐘 Running composer scripts [DONE]');
    }
}
async function ensureLocalPhp() {
    try {
        await spawnAsync('which', ['php', 'php-cgi'], { stdio: 'pipe' });
        return true;
    }
    catch (e) {
        return false;
    }
}
async function readRuntimeFile(file) {
    const blob = await build_utils_1.FileBlob.fromStream({
        stream: file.toStream(),
    });
    return blob.data.toString();
}
// *****************************************************************************
// PRIVATE API *****************************************************************
// *****************************************************************************
async function runPhp(args, opts = {}) {
    try {
        await spawnAsync('php', args, {
            ...opts,
            env: {
                ...process.env,
                ...(opts.env || {}),
                COMPOSER_HOME: '/tmp',
                PATH: `${PHP_BIN_DIR}:${process.env.PATH}`,
                PHP_INI_EXTENSION_DIR: PHP_MODULES_DIR,
                PHP_INI_SCAN_DIR: `:${path_1.default.resolve(__dirname, '../conf')}`,
                LD_LIBRARY_PATH: `${PHP_LIB_DIR}:/usr/lib64:/lib64:${process.env.LD_LIBRARY_PATH}`
            }
        });
    }
    catch (e) {
        console.error(e);
        process.exit(1);
    }
}
function spawnAsync(command, args, opts = {}) {
    return new Promise((resolve, reject) => {
        const child = (0, child_process_1.spawn)(command, args, {
            stdio: "ignore",
            ...opts
        });
        child.on('error', reject);
        child.on('exit', (code, signal) => {
            if (code === 0) {
                resolve();
            }
            else {
                reject(new Error(`Exited with ${code || signal}`));
            }
        });
    });
}
