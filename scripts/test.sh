#!/bin/bash

bold=$(tput bold)
normal=$(tput sgr0)


test_script_file_names=(

	"php_syntax"

	"php_tests"
)


clear

printf "\n\n Running tests: \n\n"

c=0
for test_file_name in "${test_script_file_names[@]}"
do
	((c=c+1)); printf "  ${c}. ${test_file_name} \n"
done

printf "\n\n\n\n\n"


c=0
for test_file_name in "${test_script_file_names[@]}"
do
	((c=c+1))

	printf "${bold}${c}. ${test_file_name}${normal}\n\n"

	if (bash "scripts/tests/${test_file_name}.sh"  \ | grep '^OK: FOUND NO PROBLEMS' -v)
	then
		exit 1;
	fi;

	printf "\n\n\n\n\n"

done
