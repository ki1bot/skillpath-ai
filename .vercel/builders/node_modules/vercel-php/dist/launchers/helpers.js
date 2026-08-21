"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.isDev = exports.getPhpDir = exports.getUserDir = void 0;
exports.normalizeEvent = normalizeEvent;
exports.transformFromAwsRequest = transformFromAwsRequest;
exports.transformToAwsResponse = transformToAwsResponse;
const path_1 = require("path");
const getUserDir = () => (0, path_1.join)(process.env.LAMBDA_TASK_ROOT || '/', 'user');
exports.getUserDir = getUserDir;
const getPhpDir = () => (0, path_1.join)(process.env.LAMBDA_TASK_ROOT || '/', 'php');
exports.getPhpDir = getPhpDir;
const isDev = () => process.env.NOW_PHP_DEV === '1';
exports.isDev = isDev;
function normalizeEvent(event) {
    if (event.Action === 'Invoke') {
        const invokeEvent = JSON.parse(event.body);
        const { method, path, host, headers = {}, encoding, } = invokeEvent;
        let { body } = invokeEvent;
        if (body) {
            if (encoding === 'base64') {
                body = Buffer.from(body, encoding);
            }
            else if (encoding === undefined) {
                body = Buffer.from(body);
            }
            else {
                throw new Error(`Unsupported encoding: ${encoding}`);
            }
        }
        return {
            method,
            path,
            host,
            headers,
            body,
        };
    }
    const { httpMethod: method, path, host, headers = {}, body, } = event;
    return {
        method,
        path,
        host,
        headers,
        body,
    };
}
async function transformFromAwsRequest({ method, path, host, headers, body, }) {
    if (!process.env.NOW_ENTRYPOINT) {
        console.error('Missing ENV NOW_ENTRYPOINT');
    }
    const entrypoint = (0, path_1.join)((0, exports.getUserDir)(), process.env.NOW_ENTRYPOINT || 'index.php');
    const uri = host + path;
    return { entrypoint, uri, path, host, method, headers, body };
}
function transformToAwsResponse({ statusCode, headers, body }) {
    return { statusCode, headers, body: body.toString('base64'), encoding: 'base64' };
}
