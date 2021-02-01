# Free Shop App

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
* NodeJS/NPM (for development)

## Installation

Run:

    composer install
    npm install

Copy .evn.example to .env and change database parameters.

Run:

    php artisan key:generate
    php artisan migrate
    php artisan db:seed

## Export language strings

Export translatable strings for any language (for example French = fr):

    php artisan translatable:export fr

Find untranslated strings in a language file:

    php artisan translatable:inspect-translations fr
