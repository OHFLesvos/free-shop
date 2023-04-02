# Free Shop App

This is a "free  web-shop" application based on [Laravel](https://laravel.com/), a popular PHP framework.

The service is intended to be used for distribution of items/goods in humanitarian aid scenarios, with a points based system for the "customers".
They receive a certain amount of credits on a regular basis which then can be used to "purchase" items from the online shop. Once an order
is marked as ready, the customer receives a notification by SMS informing about the modalities of the pickup of the order.

## Requirements

### Environment

* A web server like:
  * Apache (tested with 2.4.x)
  * Nginx
* PHP >= 8.2 with the following extensions:
  * BCMath
  * Ctype
  * Fileinfo
  * JSON
  * Mbstring
  * OpenSSL
  * PDO
  * Tokenizer
  * XML
* Composer PHP dependency manager
* A relational database like:
  * MySQL
  * MariaDB (tested with 10.4.x)
  * PostgreSQL (tested with 11.x)

### 3rd-party services

* [OPTIONAL] [Google OAuth](https://developers.google.com/identity/protocols/oauth2/web-server) for backend (administrator) login
* [REQUIRED] [Twilio](https://www.twilio.com/) for sending order updates by SMS
* [REQUIRED] [Symfony Mailer](https://symfony.com/doc/current/mailer.html)-compatible mail service
* [OPTIONAL] [Sentry](https://sentry.io/welcome/) for application monitoring
* [OPTIONAL] [Monolog](https://github.com/Seldaek/monolog)-compatible logging service for log collection and analysis

## Installation

Install PHP package dependencies:

```bash
composer install
```

Copy `.env.example` to `.env` and change database parameters and any other parameters according to your web setup.

Generate encryption key:

```bash
php artisan key:generate
```

Create/migrate database tables:

```bash
php artisan migrate
```

Cache routes and configuration for increased performance (don't use during development!):

```bash
php artisan optimize
php artisan view:clear
```

## Deployment

The code contains a [GitHub actions](https://docs.github.com/en/actions) definition to assemble and deploy the application to any web hosting server via SSH whenever changes are made to the 'production' branch. Credentials for the target server need to be provided as [GitHub Secrets](https://docs.github.com/en/actions/reference/encrypted-secrets).

## Task scheduler

Setup the task scheduler using the following cronjob:

```cron
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

More information can be found in the [Laravel documentation](https://laravel.com/docs/8.x/scheduling#running-the-scheduler).

## Setup backend access

Backend login relies on Google OAuth. Create API credentials according to [this guide](https://developers.google.com/identity/sign-in/web/sign-in).

to obtain the client ID and client secret values.

Update the `GOOGLE_*` variables in `.env` accordingly.

Access the backend via http://your-site.com/backend

## Development

## Compile assets

Install and compile JavaScript and CSS files for the public web directory:

```bash
npm install
num run dev
```

### Database seeding

To seed the database with random entries, run:

```bash
php artisan db:seed
```

### Export language strings from code

Export translatable strings for any language:

```bash
php artisan translatable:export fr,ar,fa,so
```

Find untranslated strings in a language file (for example French = fr):

```bash
php artisan translatable:inspect-translations fr
```

## Static code analysis

Run:

```bash
./vendor/bin/phpstan analyse
```

More information [here](https://github.com/nunomaduro/larastan).

## Code style fixer

Run:

```bash
./vendor/bin/pint
```

More information [here](https://github.com/laravel/pint).

## Set up development environment on WSL with Docker and Laravel Sail

Install WSL (Windows Subsystem for Linux), enable WSL version 2 and make it the default version. Run in an elevated command or PowerShell window:

```bash
wsl --install
```

See [here](https://docs.microsoft.com/en-us/windows/wsl/install) for more info.

Install Ubuntu for WSL from the Microsoft Store.

Install Docker for Windows or Rancher Desktop.

In case of Rancher Desktop, change the settings to expose Docker to Ubuntu, and fix socket permissions as needed:

```bash
sudo chmod 666 /var/run/docker.sock
sudo usermod -aG docker ${USER}
```

In case of Docker for Windows, if you get an error message like `/usr/bin/docker-credential-desktop.exe: Invalid argument`, you can do the following:

Edit the ~/.docker/config.json file `~/.docker/config.json`.

It should display something similar to this:

```json
{
    "credsStore": "desktop.exe"
}
```

Change “credsStore” to “credStore” like this:

```json
{
    "credStore": "desktop.exe"
}
```

Install Visual Studio Code and the Remote - WSL extension. Open a new WSL window, and checkout the code from Github.

Setup Laravel sail:

```bash
docker run --rm -u "$(id -u):$(id -g)" -v $(pwd):/opt -w /opt laravelsail/php82-composer:latest composer install --ignore-platform-reqs
```

Configure the sail bash alias by adding the following line to `.bashrc`:

```bash
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
```

Copy `.env.example` to `.env`.

Set

```bash
DB_HOST=mysql
```

Run the following commands:

```bash
sail up -d
sail artisan key:generate
sail artisan migrate --seed
sail npm install
sail npm run dev
```

### Recommended software for development

* [Visual Studio Code](https://code.visualstudio.com/)
* [Git](https://git-scm.com/)
* [GitHub Desktop](https://desktop.github.com/)
* [XAMPP (on Windows)](https://www.apachefriends.org/)
* [Composer](https://getcomposer.org/)
* [NodeJS/NPM](https://nodejs.org/)

## Productive docker image

Adapt the environment variables in `docker-compose-prod.yml` according to your setup.

Build the docker image:

```bash
docker-compose -f docker-compose-prod.yml build
```

Run the docker image:

```bash
docker-compose -f docker-compose-prod.yml up -d
```

Execute Laravel artisan command inside the container (if needed):

```bash
docker-compose exec app php artisan YOUR_COMMAND
```
