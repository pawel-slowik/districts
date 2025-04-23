In order to modify installed PHP development tools:

1. run Composer with the `--no-install` flag in the `php-tools` container,
2. add a Composer install step and other setup commands to the Dockerfile, if
   necessary,
3. modify the `ENTRYPOINT` and / or `CMD`, if necessary,
4. rebuild the image.

Examples:

- Add another PHPStan extension

        docker compose run --user $(id -u) --volume "$(pwd)/docker/php-tools/phpstan:/opt/phpstan" --workdir=/opt/phpstan --entrypoint=composer php-tools require --dev --no-install phpstan/phpstan-symfony:^1.4

- Update PHPStan and extensions

        docker compose run --user $(id -u) --volume "$(pwd)/docker/php-tools/phpstan:/opt/phpstan" --workdir=/opt/phpstan --entrypoint=composer php-tools update --no-install

- Add PHP CS Fixer

    First, prepare a Composer configuration dedicated for the tool:

        mkdir docker/php-tools/php-cs-fixer
        docker compose run --user $(id -u) --volume "$(pwd)/docker/php-tools/php-cs-fixer:/opt/php-cs-fixer" --workdir=/opt/php-cs-fixer --entrypoint=composer php-tools require --dev --no-install friendsofphp/php-cs-fixer:^3.49

    Then, add build steps to the Dockerfile:

        COPY php-cs-fixer/composer.json php-cs-fixer/composer.lock /opt/php-cs-fixer/
        WORKDIR /opt/php-cs-fixer
        RUN composer install
        ENV PATH="/opt/php-cs-fixer/vendor/bin:$PATH"

    Finally, modify `docker-entrypoint.sh` to include a call to `php-cs-fixer`:

        if [ $run_fixer -eq 1 ]; then
        	php-cs-fixer fix -v --dry-run --diff
        fi

- Rebuild the image

        docker compose build php-tools
