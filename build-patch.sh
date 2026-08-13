set -euo pipefail

FOUND=0
PATCHED=0

while IFS= read -r PHP_FILE; do
    FOUND=1

    echo "Checking vercel-php runtime: $PHP_FILE"

    sed -i \
        -e "s/handler: 'launcher\.launcher'/handler: 'launcher.js'/g" \
        -e 's/handler: "launcher\.launcher"/handler: "launcher.js"/g' \
        "$PHP_FILE"

    if grep -q "launcher\.launcher" "$PHP_FILE"; then
        echo "Old launcher handler still exists in: $PHP_FILE"
        exit 1
    fi

    if grep -q "launcher\.js" "$PHP_FILE"; then
        echo "Patched vercel-php handler: $PHP_FILE"
        PATCHED=1
    fi
done < <(
    find /vercel \
        -type f \
        -name "index.js" \
        -path "*vercel-php*/dist/index.js" \
        2>/dev/null
)

if [ "$FOUND" -ne 1 ]; then
    echo "No vercel-php runtime files found"
    exit 1
fi

if [ "$PATCHED" -ne 1 ]; then
    echo "vercel-php runtime was found but launcher.js handler was not verified"
    exit 1
fi

echo "All vercel-php runtime handlers verified"
