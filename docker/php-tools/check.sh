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
		echo "$0: invalid command: $1" >&2
		exit 1
		;;
esac

if [ $run_all -eq 1 ]; then
	run_phpstan=1
	run_fixer=1
	run_sniffer=1
	run_deptrac=1
fi

result=0
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
	phpcs
fi

if [ $run_deptrac -eq 1 ]; then
	deptrac --version
	deptrac analyse -c deptrac-contexts.yaml
fi

exit $result
