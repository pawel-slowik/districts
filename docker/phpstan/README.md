In order to modify the PHPStan installation:

1. run Composer with the `--no-install` flag in the PHPStan container,
2. rebuild the PHPStan image.

Examples:

    # add another PHPStan extension
    docker compose run --user $(id -u) --volume "$(pwd)/docker/phpstan:/opt/phpstan" --workdir=/opt/phpstan --entrypoint=composer phpstan require --dev --no-install phpstan/phpstan-symfony:^1.4

    # update PHPStan and extensions
    docker compose run --user $(id -u) --volume "$(pwd)/docker/phpstan:/opt/phpstan" --workdir=/opt/phpstan --entrypoint=composer phpstan update --no-install

    docker compose build phpstan
