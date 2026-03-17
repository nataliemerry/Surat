#!/bin/bash
# deploy.sh — Build & Pack Laravel (Anti-Conflict Version)

FOLDER_NAME=$(basename "$PWD")
ARCHIVE_NAME="${FOLDER_NAME}_production.tar.gz"
# Path sementara di folder luar agar tar tidak mendeteksi perubahan file sendiri
TEMP_ARCHIVE="../$ARCHIVE_NAME"

echo "==========================================="
echo "   Laravel Production Packager (Full)      "
echo "==========================================="

# 1. Clear Local Laravel Cache
echo "[1/3] Clearing application cache..."
php artisan optimize:clear > /dev/null 2>&1

# 2. Build Frontend Assets
echo "[2/3] Building Vite assets (npm run build)..."
npm run build > /dev/null 2>&1
if [ $? -ne 0 ]; then
    echo "ERROR: npm run build failed. Process aborted."
    exit 1
fi

# 3. Create the Tar Archive
echo "[3/3] Creating archive: $ARCHIVE_NAME..."
echo "Archiving progress (number of files):"

# Hapus file lama jika ada
rm -f "$ARCHIVE_NAME"
rm -f "$TEMP_ARCHIVE"

# Kita simpan output ke $TEMP_ARCHIVE (di luar folder ini)
tar -I 'gzip -1' -cf "$TEMP_ARCHIVE" \
    --checkpoint=500 \
    --checkpoint-action=echo="  > %u files packaged..." \
    --exclude="node_modules" \
    --exclude=".git" \
    --exclude=".env" \
    --exclude=".env.example" \
    --exclude=".editorconfig" \
    --exclude=".prettierrc" \
    --exclude=".eslintrc.cjs" \
    --exclude=".gitattributes" \
    --exclude=".gitignore" \
    --exclude="tests" \
    --exclude="storage/logs/*" \
    --exclude="storage/framework/cache/*" \
    --exclude="storage/framework/sessions/*" \
    --exclude="storage/framework/views/*" \
    --exclude="phpstan.neon" \
    --exclude="phpunit.xml" \
    --exclude="docker-compose.yml" \
    --exclude="package.json" \
    --exclude="package-lock.json" \
    --exclude="postcss.config.js" \
    --exclude="tailwind.config.js" \
    --exclude="vite.config.js" \
    --exclude="readme.md" \
    --exclude="LICENSE.md" \
    --exclude="screenshot.png" \
    --exclude="Procfile" \
    --exclude="deploy.sh" \
    --exclude="*.tar.gz" \
    --exclude="*.zip" \
    --exclude="vendor/**/tests" \
    --exclude="vendor/**/docs" \
    --exclude="vendor/**/*.md" \
    .

# Jika berhasil, pindahkan file dari folder luar ke folder saat ini
if [ $? -eq 0 ]; then
    mv "$TEMP_ARCHIVE" "./$ARCHIVE_NAME"
    echo "-------------------------------------------"
    echo " SUCCESS! Archive created: $ARCHIVE_NAME"
    echo "-------------------------------------------"
else
    # Hapus file temp jika gagal
    rm -f "$TEMP_ARCHIVE"
    echo "-------------------------------------------"
    echo " ERROR: Failed to create the archive."
    echo "-------------------------------------------"
fi