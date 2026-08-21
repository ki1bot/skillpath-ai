import { File, FileBlob } from '@vercel/build-utils';
export declare function getPhpFiles(): Promise<RuntimeFiles>;
export declare function getLauncherFiles(): RuntimeFiles;
export declare function modifyPhpIni(userFiles: UserFiles, runtimeFiles: RuntimeFiles): Promise<FileBlob | undefined>;
export declare function runComposerInstall(workPath: string): Promise<void>;
export declare function runComposerScripts(composerFile: File, workPath: string): Promise<void>;
export declare function ensureLocalPhp(): Promise<boolean>;
export declare function readRuntimeFile(file: File): Promise<string>;
