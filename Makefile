.PHONY: help prepare run stop migrate shell artisan db test all

help: ## Display available commands
	@echo "Firstly you need to run prepare to build docker, next you can run, finally, you need to run composer-install and migrate. You can do it all with 'all' command. when running, you can use other commands."
	@echo ""
	@echo "Usage:"
	@grep -E '^[a-zA-Z_-]+: ## ' $(MAKEFILE_LIST) | sed 's/: ## / - /'

all: composer-bootstrap prepare run composer-install migrate## Run from scratch

composer-bootstrap: ## You need to install sail from composer to run docker-compose, for those add composer container
	docker-compose run --rm composer

prepare: ## Build docker compose
	docker compose build

run: ## Run local environment
	docker compose up -d

stop: ## Stop local environment
	docker compose down

composer-install: ## Migrate database
	docker-compose exec -it  laravel.test composer install

migrate: ## Migrate database
	docker-compose exec -it  laravel.test php artisan migrate

shell: ## Run php container shell
	docker-compose exec -it  laravel.test bash

tinker: ## Run php shell
	docker-compose exec -it laravel.test php artisan tinker

db: ## Run postgress shell
	./vendor/bin/sail psql

all-test: test analysis ## Run all possible tests

test: ## Run tests
	docker-compose exec -it laravel.test php artisan test
analysis: phpcs phpstan psalm ## Run analysis tools

phpcs: ## Run phpcs
	docker-compose exec -it laravel.test php artisan phpcs:run

phpstan: ## Run phpstan
	docker-compose exec -it laravel.test php artisan phpstan:run

psalm: ## Run psalm
	docker-compose exec -it laravel.test php artisan psalm:run

make-doc: ## create openapi docs
	docker-compose exec -it laravel.test php artisan scribe:generate

redis: ## Run redis shell
	./vendor/bin/sail redis
