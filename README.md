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

Install required components:

	composer install

Start the development containers:

    docker-compose -f docker/docker-compose.yml up -d

Create the database structure:

    docker exec -it $(docker ps -q -f ancestor=districts-php) vendor/bin/doctrine orm:schema-tool:update --force

## Usage

Run the scraper to populate the database:

    docker exec -it $(docker ps -q -f ancestor=districts-php) ./console.php update --help

    docker exec -it $(docker ps -q -f ancestor=districts-php) ./console.php update

Open <http://127.0.0.1:8080> in your browser.

## TODO

- flash messages
- replace the validator with Valitron? zend-validator?
- automatically generate forms with [zend-form and model annotations](https://docs.zendframework.com/zend-form/quick-start/#using-annotations)?
- pagination
- pretty HTML / CSS - use a CSS framework
