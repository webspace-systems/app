#!/bin/bash

bold=$(tput bold)
normal=$(tput sgr0)


test_script_file_names=(

	# "php_lint"

	"php_tests"

	"php_syntax"
)


verbose=false

if [ "$1" == '-v' ]
then
	verbose=true
fi


if [[ "$verbose" = true ]]
then
	printf "\nRunning tests: \n\n"

	c=0
	for test_file_name in "${test_script_file_names[@]}"
	do
		((c=c+1)); printf "  ${c}. ${test_file_name} \n"
	done

	printf "\n"
fi


c=0
for test_file_name in "${test_script_file_names[@]}"
do
	((c=c+1))

	printf "\n\n${bold}${c}. ${test_file_name}${normal}\n\n"

	if [[ "$verbose" = true ]]
	then
		bash "scripts/tests/${test_file_name}.sh" -v || exit 1
	else
		bash "scripts/tests/${test_file_name}.sh" || exit 1
	fi

	printf "\n\n"
done

printf "\n\n\e[32mAll tests completed. No errors found. ${normal}"

printf "\n\n\n"
