In order to modify installed PHP development tools:

1. run Composer with the `--no-install` flag in the `dev-tools` container,
2. add a Composer install step and other setup commands to the Dockerfile, if
   necessary,
3. modify the `ENTRYPOINT` and / or `CMD`, if necessary,
4. rebuild the image.

Examples:

- Add another PHPStan extension

        docker compose run --user $(id -u) --volume "$(pwd)/dev-tools/phpstan:/opt/phpstan" --workdir=/opt/phpstan --entrypoint=composer dev-tools require --dev --no-install phpstan/phpstan-symfony:^1.4

- Update PHPStan and extensions

        docker compose run --user $(id -u) --volume "$(pwd)/dev-tools/phpstan:/opt/phpstan" --workdir=/opt/phpstan --entrypoint=composer dev-tools update --no-install

- Add PHP CS Fixer

    First, prepare a Composer configuration dedicated for the tool:

        mkdir dev-tools/php-cs-fixer
        docker compose run --user $(id -u) --volume "$(pwd)/dev-tools/php-cs-fixer:/opt/php-cs-fixer" --workdir=/opt/php-cs-fixer --entrypoint=composer dev-tools require --dev --no-install friendsofphp/php-cs-fixer:^3.49

    Then, add build steps to the Dockerfile:

        COPY php-cs-fixer/composer.json php-cs-fixer/composer.lock /opt/php-cs-fixer/
        WORKDIR /opt/php-cs-fixer
        RUN composer install

    Finally, modify `docker-entrypoint.sh` to include a call to `php-cs-fixer`:

        if [ $run_fixer -eq 1 ]; then
        	/opt/php-cs-fixer/vendor/bin/php-cs-fixer --config=./dev-tools/php-cs-fixer/.php-cs-fixer.php fix -v --dry-run --diff
        fi

- Rebuild the image

        docker compose build dev-tools
