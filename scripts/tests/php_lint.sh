#!/bin/bash

bold=$(tput bold)
normal=$(tput sgr0)


if ! [ -x "$(command -v phplint)" ]; then
	printf "\n\nPlease excuse program 'phplint' that is required to be installed globally...\n\n\n"
	sudo npm i -g phplint
fi


verbose=false

if [ "$1" == '-v' ]
then
	
	verbose=true

	printf "PHP Lint'ing .php & .phtml files in app/src (excl. plugins)... \n"
fi



phplint "src/**/*.php" '!*/plugins/**' # -s

