set -euo pipefail

PHP_FILE=$(find /vercel \
    -type f \
    -name "index.js" \
    -path "*vercel-php*/dist/index.js" \
    -print \
    -quit \
    2>/dev/null)

if [ -z "$PHP_FILE" ]; then
    echo "vercel-php runtime file not found"
    exit 1
fi

echo "Found vercel-php runtime: $PHP_FILE"

sed -i \
    -e "s/handler: 'launcher\.launcher'/handler: 'launcher.js'/g" \
    -e 's/handler: "launcher\.launcher"/handler: "launcher.js"/g' \
    "$PHP_FILE"

if grep -q "launcher\.launcher" "$PHP_FILE"; then
    echo "Failed: old launcher.launcher handler still exists"
    exit 1
fi

if ! grep -q "launcher\.js" "$PHP_FILE"; then
    echo "Failed: launcher.js handler was not found"
    exit 1
fi

echo "Patched vercel-php handler successfully"
