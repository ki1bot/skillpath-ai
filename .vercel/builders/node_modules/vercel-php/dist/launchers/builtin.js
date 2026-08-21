"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const http_1 = __importDefault(require("http"));
const child_process_1 = require("child_process");
const net_1 = __importDefault(require("net"));
const helpers_1 = require("./helpers");
const path_1 = require("path");
let server;
async function startServer(entrypoint) {
    var _a, _b, _c;
    // Resolve document root and router
    const router = entrypoint;
    const docroot = (0, path_1.join)((0, helpers_1.getUserDir)(), (_a = process.env.VERCEL_PHP_DOCROOT) !== null && _a !== void 0 ? _a : '');
    console.log(`🐘 Spawning: PHP Built-In Server at ${docroot} (document root) and ${router} (router)`);
    // php spawn options
    const options = {
        stdio: ['pipe', 'pipe', 'pipe'],
        env: {
            ...process.env,
            LD_LIBRARY_PATH: `/var/task/lib:${process.env.LD_LIBRARY_PATH}`
        }
    };
    // now vs now-dev
    if (!(0, helpers_1.isDev)()) {
        options.env.PATH = `${(0, helpers_1.getPhpDir)()}:${process.env.PATH}`;
        options.cwd = (0, helpers_1.getPhpDir)();
    }
    else {
        options.cwd = (0, helpers_1.getUserDir)();
    }
    // We need to start PHP built-in server with following setup:
    // php -c php.ini -S ip:port -t /var/task/user /var/task/user/foo/bar.php
    //
    // Path to document root lambda task folder with user prefix, because we move all
    // user files to this folder.
    //
    // Path to router is absolute path, because CWD is different.
    //
    server = (0, child_process_1.spawn)('php', ['-c', 'php.ini', '-S', '0.0.0.0:3000', '-t', docroot, router], options);
    (_b = server.stdout) === null || _b === void 0 ? void 0 : _b.on('data', data => {
        console.log(`🐘STDOUT: ${data.toString()}`);
    });
    (_c = server.stderr) === null || _c === void 0 ? void 0 : _c.on('data', data => {
        console.error(`🐘STDERR: ${data.toString()}`);
    });
    server.on('close', function (code, signal) {
        console.log(`🐘 PHP Built-In Server process closed code ${code} and signal ${signal}`);
    });
    server.on('error', function (err) {
        console.error(`🐘 PHP Built-In Server process errored ${err}`);
    });
    await whenPortOpens(3000, 500);
    process.on('exit', () => {
        server.kill();
    });
    return server;
}
async function query({ entrypoint, uri, path, headers, method, body }) {
    if (!server) {
        await startServer(entrypoint);
    }
    return new Promise(resolve => {
        const options = {
            hostname: 'localhost',
            port: 3000,
            path,
            method,
            headers,
        };
        console.log(`🐘 Accessing ${uri}`);
        console.log(`🐘 Querying ${path}`);
        const req = http_1.default.request(options, (res) => {
            const chunks = [];
            res.on('data', (data) => {
                chunks.push(data);
            });
            res.on('end', () => {
                resolve({
                    statusCode: res.statusCode || 200,
                    headers: res.headers,
                    body: Buffer.concat(chunks)
                });
            });
        });
        req.on('error', (error) => {
            console.error('🐘 PHP Built-In Server HTTP errored', error);
            resolve({
                body: Buffer.from(`PHP Built-In Server HTTP error: ${error}`),
                headers: {},
                statusCode: 500
            });
        });
        if (body) {
            req.write(body);
        }
        req.end();
    });
}
function whenPortOpensCallback(port, attempts, cb) {
    const client = net_1.default.connect(port, '127.0.0.1');
    client.on('error', (error) => {
        if (!attempts)
            return cb(error);
        setTimeout(() => {
            whenPortOpensCallback(port, attempts - 1, cb);
        }, 10);
    });
    client.on('connect', () => {
        client.destroy();
        cb();
    });
}
function whenPortOpens(port, attempts) {
    return new Promise((resolve, reject) => {
        whenPortOpensCallback(port, attempts, (error) => {
            if (error) {
                reject(error);
            }
            else {
                resolve();
            }
        });
    });
}
async function launcher(event) {
    const awsRequest = (0, helpers_1.normalizeEvent)(event);
    const input = await (0, helpers_1.transformFromAwsRequest)(awsRequest);
    const output = await query(input);
    return (0, helpers_1.transformToAwsResponse)(output);
}
exports.launcher = launcher;
// (async function () {
//   const response = await launcher({
//     Action: "test",
//     httpMethod: "GET",
//     body: "",
//     path: "/",
//     host: "https://vercel.com",
//     headers: {
//       'HOST': 'vercel.com'
//     },
//     encoding: null,
//   });
//   console.log(response);
// })();
