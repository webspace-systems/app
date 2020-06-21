#!/bin/bash

bold=$(tput bold)
normal=$(tput sgr0)


interprete_empty_response_as_success=true


verbose=false

if [ "$1" == '-v' ]
then
	
	verbose=true

	printf "Looping php test scripts: tests/*.php... \n"
fi


if [ "$interprete_empty_response_as_success" = true ] && [ "$verbose" = true ]
then
	printf "\ninterprete_empty_response_as_success = true\n"
fi


for filepath in $(find tests -name '*.php')
do
    [ -e "$filepath" ] || continue

	result=$(php -f "$filepath")

	if ( [[ $result == *"K: FOUND NO ERRORS"* ]] || [ "$result" = "OK" ] )
	then

		if [ "$verbose" = true ]
		then
	    	printf "\n\e[32m OK  ${normal} $filepath"
	    else
	    	printf "."
		fi

    else

    	if [ -z "$result" ] # is empty
    	then

			if [ "$interprete_empty_response_as_success" = true ]
			then

				if [ "$verbose" = true ]
				then
		    		printf "\n\e[32m OK  ${normal} $filepath"
		    	else
		    		printf "."
		    	fi

	    	else
	    		printf "\n\e[31m ERR ${normal} $filepath: \e[31m Empty response ${normal}"
				printf "\n\n\n"
				exit 1
			fi

		else

    		printf "\n\e[31m ERR ${normal} $filepath: \e[31m ${result} ${normal}"
    		printf "\n\n\n"
    		exit 1
    	fi
    fi

done


if [ "$verbose" = true ]
then
	printf "\n\n"
else
	printf "\n"
fi

printf "Done. No errors found."

