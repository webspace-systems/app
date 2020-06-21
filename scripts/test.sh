#!/bin/bash

bold=$(tput bold)
normal=$(tput sgr0)


verbose=false

if [ "$1" == '-v' ]
then
	verbose=true
fi


bash "scripts/tests/${test_file_name}.sh" -v || exit 1


printf "\n\n\e[32m"

printf "Done"

printf "${normal}\n\n\n"
