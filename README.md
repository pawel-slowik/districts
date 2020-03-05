Scrape city districts data and save it to a SQL database.

Provide a simple web interface for editing scraped data.

Note: originally, this was a job interview task. Now it is a playground for
trying out tools, frameworks, patterns etc.

## Installation

Install required components:

	composer install

Configure a developement database connection:

    export $(< env-dev)

Create the database structure:

    vendor/bin/doctrine orm:schema-tool:update --force

## Usage

Run the scraper to populate the database:

    ./console.php update --help

    ./console.php update

Start the web interface:

    php -S localhost:8080 -t public public/index.php

## TODO

- flash messages
- replace the validator with Valitron? zend-validator?
- automatically generate forms with [zend-form and model annotations](https://docs.zendframework.com/zend-form/quick-start/#using-annotations)?
- more tests
