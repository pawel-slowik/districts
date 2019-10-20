Scrape city districts data and save it to a SQL database.

Provide a simple web interface for editing scraped data.

Note: this is a job interview task.

## Installation

Install required components:

	composer install

Configure a database connection:

    cp db-config.php.dist db-config.php

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
- progress report for the update CLI command
- select cities for updating
- replace the validator with Valitron? zend-validator?
- automatically generate forms with [zend-form and model annotations](https://docs.zendframework.com/zend-form/quick-start/#using-annotations)?
- more tests
