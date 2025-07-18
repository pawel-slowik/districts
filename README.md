![Build Status][build-badge]
[![Coverage][coverage-badge]][coverage-url]

[build-badge]: https://github.com/pawel-slowik/districts/workflows/tests/badge.svg
[coverage-badge]: https://codecov.io/gh/pawel-slowik/districts/branch/master/graph/badge.svg
[coverage-url]: https://codecov.io/gh/pawel-slowik/districts

Scrape city districts data and save it to a SQL database.

Provide a simple web interface for editing scraped data.

Note: originally, this was a job interview task. Now it is a playground for
trying out tools, frameworks, patterns etc.

## Installation

Build the development containers:

    docker compose build

Install required components:

    docker compose run --rm php-fpm composer install

Create the database structure:

    docker compose run --rm php-fpm bin/doctrine orm:schema-tool:update --force

## Usage

Run the scraper to populate the database:

    docker compose run --rm php-fpm bin/console import --help

    docker compose run --rm php-fpm bin/console import

Start the development containers:

    docker compose up -d

Open <http://127.0.0.1:8080> in your browser.

You can set the `HTTP_PORT` environment variable to a different port number if
the default conflicts with some other service.

## Development

Run tests with:

    docker compose run --rm php-fpm composer test

Run static analysis and coding style checks with:

    docker compose run --rm php-tools
