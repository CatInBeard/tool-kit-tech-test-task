.PHONY: help prepare run stop migrate shell artisan db test

help: ## Display available commands
	@echo "Firstly you need to run prepare to install dependecies, next you can run, when running, you can use other commands."
	@echo ""
	@echo "Usage:"
	@grep -E '^[a-zA-Z_-]+: ## ' $(MAKEFILE_LIST) | sed 's/: ## / - /'

prepare: ## Install local env running dependecies
	composer install

run: migrate ## Run local environment
	./vendor/bin/sail up -d

stop: ## Stop local environment
	./vendor/bin/sail down

migrate: ## Migrate database
	./vendor/bin/sail artisan migrate

shell: ## Run php container shell
	./vendor/bin/sail shell
	
artisan: ## Run php shell
	./vendor/bin/sail artisan

db: ## Run postgress shell
	./vendor/bin/sail psql

test: ## Run tests
	./vendor/bin/sail artisan test
	
redis: ## Run redis shell
	./vendor/bin/sail redis
