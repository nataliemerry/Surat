#!/bin/bash
# ============================================================
# deploy.sh — Build & zip proyek Laravel untuk upload ke cPanel
# Jalankan: bash deploy.sh
# ============================================================

# Nama folder sekarang (otomatis)
FOLDER_NAME=$(basename "$PWD")
ZIP_NAME="${FOLDER_NAME}.zip"

echo "=============================="
echo "  Laravel Deploy Zipper"
echo "=============================="

# 1. Build aset Vite
echo ""
echo "[1/2] Building Vite assets..."
npm run build
if [ $? -ne 0 ]; then
    echo "ERROR: npm run build gagal. Pastikan node_modules sudah terinstall."
    exit 1
fi

# 2. Hapus zip lama jika ada
if [ -f "$ZIP_NAME" ]; then
    echo "Menghapus zip lama: $ZIP_NAME"
    rm "$ZIP_NAME"
fi

# 3. Buat zip (dari direktori parent)
echo ""
echo "[2/2] Membuat zip: $ZIP_NAME ..."
cd ..
zip -r "${FOLDER_NAME}/${ZIP_NAME}" "${FOLDER_NAME}" \
    --exclude "${FOLDER_NAME}/node_modules/*" \
    --exclude "${FOLDER_NAME}/.git/*" \
    --exclude "${FOLDER_NAME}/${ZIP_NAME}" \
    --exclude "${FOLDER_NAME}/.env" \
    --exclude "${FOLDER_NAME}/tests/*" \
    --exclude "${FOLDER_NAME}/docker-compose.*" \
    --exclude "${FOLDER_NAME}/screenshot.png" \
    --exclude "${FOLDER_NAME}/deploy.bat" \
    --exclude "${FOLDER_NAME}/deploy.sh" \
    --exclude "${FOLDER_NAME}/README.md" \
    --exclude "${FOLDER_NAME}/readme.md" \
    --exclude "${FOLDER_NAME}/phpunit.xml" \
    --exclude "${FOLDER_NAME}/phpstan.neon"

cd "${FOLDER_NAME}"

if [ $? -eq 0 ]; then
    echo ""
    echo "=============================="
    echo "  SELESAI! File siap upload:"
    echo "  $ZIP_NAME"
    echo "=============================="
else
    echo "ERROR: Gagal membuat zip."
    exit 1
fi
