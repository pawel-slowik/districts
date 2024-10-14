In order to modify installed PHP development tools:

1. run Composer with the `--no-install` flag in the `php-tools` container,
2. rebuild the image.

Examples:

    # add another PHPStan extension
    docker compose run --user $(id -u) --volume "$(pwd)/docker/php-tools/phpstan:/opt/phpstan" --workdir=/opt/phpstan --entrypoint=composer php-tools require --dev --no-install phpstan/phpstan-symfony:^1.4

    # update PHPStan and extensions
    docker compose run --user $(id -u) --volume "$(pwd)/docker/php-tools/phpstan:/opt/phpstan" --workdir=/opt/phpstan --entrypoint=composer php-tools update --no-install

    docker compose build php-tools
