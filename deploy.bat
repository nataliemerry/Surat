@echo off
:: ============================================================
:: deploy.bat — Build & zip proyek Laravel untuk upload ke cPanel
:: Double-click atau jalankan dari terminal
:: ============================================================

setlocal enabledelayedexpansion

:: Ambil nama folder otomatis
for %%I in (.) do set FOLDER_NAME=%%~nxI
set ZIP_NAME=%FOLDER_NAME%.zip

echo ==============================
echo   Laravel Deploy Zipper
echo ==============================

:: 1. Build aset Vite
echo.
echo [1/2] Building Vite assets...
call npm run build
if %ERRORLEVEL% neq 0 (
    echo.
    echo ERROR: npm run build gagal.
    echo Pastikan node_modules sudah terinstall ^(npm install^).
    pause
    exit /b 1
)

:: 2. Hapus zip lama jika ada
if exist "%ZIP_NAME%" (
    echo Menghapus zip lama: %ZIP_NAME%
    del /f "%ZIP_NAME%"
)

:: 3. Tulis script PowerShell sementara ke temp
set PS_TEMP=%TEMP%\deploy_zip_%RANDOM%.ps1
(
    echo $src = Get-Location
    echo $zipPath = Join-Path $src "%ZIP_NAME%"
    echo $folderName = "%FOLDER_NAME%"
    echo $exclude = @('node_modules', '.git', '%ZIP_NAME%', 'deploy_zip_*.ps1', '.env', 'tests', 'docker-compose.yml', 'docker-compose.override.yml', 'screenshot.png', 'deploy.bat', 'deploy.sh', 'readme.md', 'phpunit.xml', 'phpstan.neon'^)
    echo $tmpDir = Join-Path $env:TEMP ("deploy_" + [System.Guid]::NewGuid(^).ToString(^)^)
    echo $dest = Join-Path $tmpDir $folderName
    echo New-Item -ItemType Directory -Path $dest -Force ^| Out-Null
    echo $items = Get-ChildItem -Path $src -Force ^| Where-Object { $_.Name -notin $exclude }
    echo foreach ($item in $items^) { Copy-Item -Path $item.FullName -Destination $dest -Recurse -Force }
    echo Compress-Archive -Path $dest -DestinationPath $zipPath -Force
    echo Remove-Item -Recurse -Force $tmpDir
    echo Write-Host "ZIP selesai: %ZIP_NAME%"
) > "%PS_TEMP%"

:: 4. Jalankan PowerShell script
echo.
echo [2/2] Membuat zip: %ZIP_NAME% ...
powershell -NoProfile -ExecutionPolicy Bypass -File "%PS_TEMP%"
set PS_EXIT=%ERRORLEVEL%

:: Bersihkan file temp
del /f "%PS_TEMP%" 2>nul

if %PS_EXIT% neq 0 (
    echo.
    echo ERROR: Gagal membuat zip. Coba jalankan PowerShell sebagai Administrator.
    pause
    exit /b 1
)

echo.
echo ==============================
echo   SELESAI! File siap upload:
echo   %ZIP_NAME%
echo ==============================
pause
