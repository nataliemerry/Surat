# Surat

Surat is a Laravel-based application for managing outgoing letters and related administrative data.

## Requirements

- PHP 8.2+ with required Laravel extensions
- Composer
- Node.js 18+ and npm
- MySQL/MariaDB (or another database supported by Laravel)

## Local Setup

1. Clone the repository and enter the project directory.

```sh
git clone <your-repository-url> Surat
cd Surat
```

2. Install backend and frontend dependencies.

```sh
composer install
npm install
```

3. Create the environment file.

```sh
cp .env.example .env
```

4. Configure your database in the `.env` file.

Important variables:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=surat
DB_USERNAME=root
DB_PASSWORD=
```

5. Generate an application key.

```sh
php artisan key:generate
```

6. Run database migrations (and optional seeders).

```sh
php artisan migrate
# optional
php artisan db:seed
```

7. Run the app in development mode.

```sh
# terminal 1
php artisan serve

# terminal 2
npm run dev
```

Open the URL shown by `php artisan serve`.

## Useful Development Commands

```sh
php artisan optimize:clear
php artisan test
npm run build
```

## Deploy Script (`deploy.sh`)

This project includes a deployment packaging script: `deploy.sh`.

### What `deploy.sh` does

The script creates a production archive named:

```text
<project_folder_name>_production.tar.gz
```

Execution flow:

1. Clears Laravel caches using `php artisan optimize:clear`.
2. Builds frontend assets using `npm run build`.
3. Packages the application into a `.tar.gz` archive.

The archive excludes development and sensitive files/folders such as:

- `.git`, `node_modules`, test files
- local environment files (`.env`)
- frontend build config files
- logs and temporary framework caches
- previous archive files (`*.tar.gz`, `*.zip`)

This makes the output archive smaller and safer to upload to production hosting (for example, cPanel).

### How to use `deploy.sh`

1. Make sure you are in the project root.
2. Run the script:

```sh
./deploy.sh
```

If needed, make it executable first:

```sh
chmod +x deploy.sh
./deploy.sh
```

3. Wait until you see the success message.
4. Upload the generated `*_production.tar.gz` file to your server and extract it there.

### Notes

- If `npm run build` fails, the script stops automatically.
- The script does not upload files automatically; it only prepares a deployment-ready archive.

## License

This project is licensed under the terms in `LICENSE.md`.
