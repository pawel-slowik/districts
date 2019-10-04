Scrape city districts data and save it to a SQL database.

Note: this is a job interview task.

## Installation

Install required components:

	composer install

Configure a database connection:

    cp db-config.php.dist db-config.php

Create the database structure:

    vendor/bin/doctrine orm:schema-tool:update --force

## Usage

    ./console.php update --help

    ./console.php update
