# PPM BACKEND
An API for PPM apps
<br><br>

## Requirements
* Laravel 8.x (PHP 7.x)
* NodeJS > 14
* Composer 2

## How to install

### Clone Repository
open your terminal, go to the directory that you will install this project, then run the following command:

```bash
git clone https://github.com/caatis-coe/ppm-backend.git

cd ppm-backend
```

### Install packages
Install vendor using composer

```bash
composer install
```

### Configure .env
Copy .env.example file

```bash
cp .env.example .env
```

Then run the following command :

```php
php artisan key:generate
```

### Public Disk
To make these files accessible from the web, you should create a symbolic link from public/storage to storage/app/public.
To create the symbolic link, you may use the storage:link Artisan command:

```php
php artisan storage:link
```

### Running Application
To serve the laravel app, you need to run the following command in the project director (This will serve your app, and give you an adress with port number 8000 or etc)
- **Note: You need run the following command into new terminal tab**

```php
php artisan serve
```

## Demo
https://ppm.matradipti.org/
