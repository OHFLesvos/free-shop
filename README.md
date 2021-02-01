# Free Shop App

This is a web-shop application based on [Laravel](https://laravel.com/), a popular PHP framework.

## Requirements

* PHP >= 7.4
  * BCMath PHP Extension
  * Ctype PHP Extension
  * Fileinfo PHP Extension
  * JSON PHP Extension
  * Mbstring PHP Extension
  * OpenSSL PHP Extension
  * PDO PHP Extension
  * Tokenizer PHP Extension
  * XML PHP Extension
* Composer
* MySQL, MariaDB or PostgreSQL database

## Recommended software for development

* [Visual Studio Code](https://code.visualstudio.com/)
* [Git](https://git-scm.com/)
* [GitHub Desktop](https://desktop.github.com/)
* [XAMPP (on Windows)](https://www.apachefriends.org/)
* [Composer](https://getcomposer.org/)
* [NodeJS/NPM](https://nodejs.org/)

## Installation

Install PHP package dependencies:

    composer install

Copy `.env.example` to `.env` and change database parameters and any other parameters according to your web setup.

Generate encryption key:

    php artisan key:generate

Create/migrate database tables:

    php artisan migrate

Seed the database with random entries (for development):

    php artisan db:seed

## Export language strings from code

Export translatable strings for any language (for example French = fr):

    php artisan translatable:export fr

Find untranslated strings in a language file:

    php artisan translatable:inspect-translations fr
