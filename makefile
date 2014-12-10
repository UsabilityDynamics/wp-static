## Build Plugin
##
##

NAME = wp-static

# Default Install Action
default:
	make install

# Build for Distribution
build:
	echo Building $(NAME).
	npm install --production
	composer install --prefer-dist --no-dev --no-interaction
	grunt build

# Install for Staging/Development
install:
	echo Installing $(NAME).
	npm install --production
	npm install --development
	composer install --prefer-source --dev --no-interaction
	grunt install
