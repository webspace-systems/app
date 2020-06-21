#!/bin/bash

bold=$(tput bold)
normal=$(tput sgr0)


verbose=false

if [ "$1" == '-v' ]
then
	
	verbose=true

	printf "Syntax checking .php & .phtml files in app/src (excl. plugins)... \n"
fi


for filepath in $( find src \( -name "*.php" -or -name "*.phtml" \) -not -path "*/plugins/*" )
do
	result=$(php -l "$filepath")

	if [[ $result == *"No syntax errors"* ]]
	then

		if [[ "$verbose" = true ]]
		then
	    	printf "\n\e[32m OK  ${normal} $filepath"
	    else
	    	printf "."
	    fi

    else

		printf "\n\e[31m ERR ${normal} $filepath"

		printf "\n\n\n\e[31m\e[3m${result}"

		printf "\n\n\n\n${normal}"

		exit 1
	fi
done


if [ "$verbose" = true ]
then
	printf "\n\n"
else
	printf "\n"
fi


printf "Done. No errors found."

