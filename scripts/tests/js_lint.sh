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

	printf "Lint'ing .js files in app/src (excl. plugins)... \n"
fi




