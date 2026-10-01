#!/bin/sh

run_phpstan=0
run_fixer=0
run_sniffer=0
run_deptrac=0
run_rector=0
run_infection=0
run_all=0
have_extra_args=0

case "$1" in
	"phpstan")
		run_phpstan=1
		shift
		;;
	"fixer")
		run_fixer=1
		shift
		;;
	"sniffer")
		run_sniffer=1
		shift
		;;
	"deptrac")
		run_deptrac=1
		shift
		;;
	"rector")
		run_rector=1
		shift
		;;
	"infection")
		run_infection=1
		shift
		;;
	"")
		run_all=1
		;;
	*)
		# https://github.com/docker-library/official-images/blob/master/README.md#consistency
		# Ensure that `docker run official-image bash` (or `sh`) works too.
		exec "${@}"
		;;
esac

if [ $run_all -eq 1 ]; then
	run_phpstan=1
	run_fixer=1
	run_sniffer=1
	run_deptrac=1
	run_rector=1
	run_infection=1
fi

if [ $run_all -eq 0 ]; then
	if [ $# -gt 0 ]; then
		have_extra_args=1
	fi
fi

result=0
# BusyBox supports traps on ERR since version 1.35.0, 2021-12-26
# shellcheck disable=SC3047
trap result=1 ERR

if [ $run_phpstan -eq 1 ]; then
	/opt/phpstan/vendor/bin/phpstan -V
	if [ $have_extra_args -eq 1 ]; then
		/opt/phpstan/vendor/bin/phpstan --configuration=./dev-tools/phpstan/phpstan.neon "${@}"
	else
		/opt/phpstan/vendor/bin/phpstan --configuration=./dev-tools/phpstan/phpstan.neon analyse
	fi
fi

if [ $run_fixer -eq 1 ]; then
	if [ $have_extra_args -eq 1 ]; then
		/opt/php-cs-fixer/vendor/bin/php-cs-fixer --config=./dev-tools/php-cs-fixer/.php-cs-fixer.php "${@}"
	else
		/opt/php-cs-fixer/vendor/bin/php-cs-fixer --config=./dev-tools/php-cs-fixer/.php-cs-fixer.php fix -v --dry-run --diff
	fi
fi

if [ $run_sniffer -eq 1 ]; then
	/opt/php_codesniffer/vendor/bin/phpcs --version
	if [ $have_extra_args -eq 1 ]; then
		/opt/php_codesniffer/vendor/bin/phpcs --standard=./dev-tools/php_codesniffer/phpcs.xml "${@}"
	else
		/opt/php_codesniffer/vendor/bin/phpcs --standard=./dev-tools/php_codesniffer/phpcs.xml -p
	fi
fi

if [ $run_deptrac -eq 1 ]; then
	/opt/deptrac/vendor/bin/deptrac --version
	if [ $have_extra_args -eq 1 ]; then
		/opt/deptrac/vendor/bin/deptrac analyse -c ./dev-tools/deptrac/deptrac-contexts.yaml "${@}"
	else
		/opt/deptrac/vendor/bin/deptrac analyse -c ./dev-tools/deptrac/deptrac-contexts.yaml
	fi
fi

if [ $run_rector -eq 1 ]; then
	/opt/rector/vendor/bin/rector -V
	if [ $have_extra_args -eq 1 ]; then
		/opt/rector/vendor/bin/rector process --config=./dev-tools/rector/rector.php "${@}"
	else
		/opt/rector/vendor/bin/rector process --config=./dev-tools/rector/rector.php --dry-run
	fi
fi

if [ $run_infection -eq 1 ]; then
	if [ $have_extra_args -eq 1 ]; then
		/opt/infection/vendor/bin/infection --configuration=./dev-tools/infection/infection.json5 "${@}"
	else
		/opt/infection/vendor/bin/infection --configuration=./dev-tools/infection/infection.json5 --with-uncovered -vv
	fi
fi

exit $result
