#!/bin/sh

result=0
trap result=1 ERR

phpstan -V
phpstan analyse

php-cs-fixer fix -v --dry-run --diff

phpcs --version
phpcs

deptrac --version
deptrac analyse -c deptrac-contexts.yaml

exit $result
