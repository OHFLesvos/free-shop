# Free Shop App

This is a web-shop application based on [Laravel](https://laravel.com/), a popular PHP framework.

## Requirements

### Environment

* A web server like
    * Apache (tested with 2.4)
    * Nginx
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
* Composer PHP dependency manager
* A relational database like:
  * MySQL
  * MariaDB (tested with 10.4.x)
  * PostgreSQL (tested with 11.x)

### 3rd-party services

* [REQUIRED] [Google OAuth](https://developers.google.com/identity/protocols/oauth2/web-server) for backend (administrator) login
* [REQUIRED] [Twilio](https://www.twilio.com/) for sending order updates by SMS
* [REQUIRED] [SwiftMailer](https://swiftmailer.symfony.com/)-compatible mail service (SMTP, Mailgun, Postmark, Amazon SES, sendmail, ...)
* [OPTIONAL] [Sentry](https://sentry.io/welcome/) for application monitoring
* [OPTIONAL] [Monolog](https://github.com/Seldaek/monolog)-compatible logging service

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

## Deployment

The code contains a [GitHub actions](https://docs.github.com/en/actions) definition to assemble and deploy the application to any web hosting server via SSH whenever changes are made to the 'production' branch. Credentials for the target server need to be provided as [GitHub Secrets](https://docs.github.com/en/actions/reference/encrypted-secrets).

## Setup backend access

Backend login relies on Google OAuth. Create API credentials according to the following guide:

https://developers.google.com/identity/sign-in/web/sign-in

to obtain the client ID and client secret values.

Update the `GOOGLE_*` variables in `.env` accordingly.

Access the backend via http://your-site.com/backend

## Export language strings from code

Export translatable strings for any language:

    php artisan translatable:export fr,ar,fa

Find untranslated strings in a language file (for example French = fr):

    php artisan translatable:inspect-translations fr

## Recommended software for development

* [Visual Studio Code](https://code.visualstudio.com/)
* [Git](https://git-scm.com/)
* [GitHub Desktop](https://desktop.github.com/)
* [XAMPP (on Windows)](https://www.apachefriends.org/)
* [Composer](https://getcomposer.org/)
* [NodeJS/NPM](https://nodejs.org/)
