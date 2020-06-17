#!/bin/bash

bold=$(tput bold)
normal=$(tput sgr0)

printf "Syntax checking .php & .phtml files in app/src (excl. plugins)... \n\n\n";

printf "\e[31m"; # red

if (find src -type f \( -name "*.php" -or -name "*.phtml" \) -not -path "*/plugins/*" -exec php -l '{}' \; | grep '^No syntax errors' -v )
then
	printf "${normal}";

	printf "\n\n\n\n${bold}";

	printf "Test failed. See errors above";

	printf "${normal}\n\n\n";

	exit 1;
fi;

printf "\e[32m"; # green

printf "OK: FOUND NO PROBLEMS";

printf "${normal}";
