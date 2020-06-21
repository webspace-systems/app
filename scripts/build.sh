#!/bin/bash

babel src -d dist -D -x [.js] --no-comments --ignore [plugins/*] --verbose
