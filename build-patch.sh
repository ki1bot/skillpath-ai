set -e

PHP_FILE=$(find /vercel -type f -name "index.js" -path "*vercel-php*/dist/index.js" 2>/dev/null | head -1)

if [ -z "$PHP_FILE" ]; then
    echo "vercel-php runtime file not found"
    exit 1
fi

echo "Found vercel-php runtime: $PHP_FILE"

sed -i "s/handler: 'launcher.launcher'/handler: 'launcher.js'/g" "$PHP_FILE"

if grep -q "handler: 'launcher.js'" "$PHP_FILE"; then
    echo "Patched vercel-php handler successfully"
else
    echo "Failed to patch vercel-php handler"
    exit 1
fi

