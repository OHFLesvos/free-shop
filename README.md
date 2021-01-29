# Free Shop App

Run:

    composer install
    npm install

Copy .evn.example to .env and change database parameters.

Run:

    php artisan key:generate
    php artisan migrate
    php artisan db:seed

## Export language strings

Export translatable strings:

    php artisan translatable:export fr

Find untranslated strings in a language file:

    php artisan translatable:inspect-translations fr
