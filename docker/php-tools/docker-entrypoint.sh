#!/bin/sh

run_phpstan=0
run_fixer=0
run_sniffer=0
run_deptrac=0
run_all=0

case "$1" in
	"phpstan")
		run_phpstan=1
		;;
	"fixer")
		run_fixer=1
		;;
	"sniffer")
		run_sniffer=1
		;;
	"deptrac")
		run_deptrac=1
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
fi

result=0
# BusyBox supports traps on ERR since version 1.35.0, 2021-12-26
# shellcheck disable=SC3047
trap result=1 ERR

if [ $run_phpstan -eq 1 ]; then
	phpstan -V
	phpstan analyse
fi

if [ $run_fixer -eq 1 ]; then
	php-cs-fixer fix -v --dry-run --diff
fi

if [ $run_sniffer -eq 1 ]; then
	phpcs --version
	phpcs -p
fi

if [ $run_deptrac -eq 1 ]; then
	deptrac --version
	deptrac analyse -c deptrac-contexts.yaml
fi

exit $result
