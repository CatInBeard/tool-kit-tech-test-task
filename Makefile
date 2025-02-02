.PHONY: help run stop migrate shell artisan db redis test all test-all copy-env generate-keys generate-app-key generate-jwt-secret composer-bootstrap build-frontend npm-install npm-build storage-link

help: ## Display available commands
	@echo "Firstly you need to run to build docker, next you can run, finally, you need to run composer-install and migrate. You can do it all with 'all' command. when running, you can use other commands."
	@echo ""
	@echo "Usage:"
	@grep -E '^[a-zA-Z_-]+: ## ' $(MAKEFILE_LIST) | sed 's/: ## / - /'

all: composer-bootstrap copy-env run composer-install generate-keys migrate seed-db storage-link build-frontend ## Run from scratch

copy-env: ## Copy env from template
	@if [ ! -f .env ]; then \
		cp .env.example .env; \
	fi

storage-link: ## Add access to storage
	php artisan storage:link

seed-db: ## Add db seed
	./vendor/bin/sail artisan db:seed

generate-keys: generate-app-key generate-jwt-secret ## Generate app keys

generate-app-key: ## Generate app key
	./vendor/bin/sail artisan key:generate

generate-jwt-secret: ## Generate jwt secret
	./vendor/bin/sail artisan jwt:secret


composer-bootstrap: ## You need to install sail from composer to run docker compose, for those add composer container
	docker compose run --rm composer

run: ## Run local environment
	./vendor/bin/sail up -d

stop: ## Stop local environment
	./vendor/bin/sail down

composer-install: ## Migrate database
	docker compose exec -it  laravel.test composer install

migrate: ## Migrate database
	./vendor/bin/sail artisan migrate

shell: ## Run php container shell
	docker compose exec -it  laravel.test bash

build-frontend: npm-install npm-build ## Build frontend

npm-install: ## Run npm install
	docker compose exec -it laravel.test npm install

npm-build: ## Run npm build
	docker compose exec -it laravel.test npm run build

tinker: ## Run php shell
	docker compose exec -it laravel.test php artisan tinker

db: ## Run postgress shell
	./vendor/bin/sail psql

test-all: test analysis ## Run all possible tests

test: ## Run tests
	docker compose exec -it laravel.test php artisan test
analysis: phpcs phpstan psalm ## Run analysis tools

phpcs: ## Run phpcs
	docker compose exec -it laravel.test php artisan phpcs:run

phpstan: ## Run phpstan
	docker compose exec -it laravel.test php artisan phpstan:run

psalm: ## Run psalm
	docker compose exec -it laravel.test php artisan psalm:run

make-doc: ## create openapi docs
	docker compose exec -it laravel.test php artisan scribe:generate

redis: ## Run redis shell
	./vendor/bin/sail redis
