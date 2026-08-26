SAIL = ./vendor/bin/sail

.PHONY: help setup up down restart migrate fresh seed dev shell logs lint test

help: ## Lista os comandos disponíveis
	@grep -E '^[a-zA-Z_-]+:.*?## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  %-10s %s\n", $$1, $$2}'

setup: ## Primeira execução: .env, composer, containers, key, migrate, npm
	test -f .env || cp .env.example .env
	docker run --rm -v "$$(pwd):/var/www/html" -w /var/www/html laravelsail/php83-composer:latest composer install
	$(SAIL) up -d
	grep -q '^APP_KEY=$$' .env && $(SAIL) artisan key:generate || true
	$(SAIL) artisan migrate --seed
	$(SAIL) npm install

up: ## Sobe os containers
	$(SAIL) up -d

down: ## Derruba os containers
	$(SAIL) down

restart: down up ## Reinicia os containers

migrate: ## Roda as migrations
	$(SAIL) artisan migrate

fresh: ## Recria o banco do zero com seed
	$(SAIL) artisan migrate:fresh --seed

seed: ## Roda os seeders
	$(SAIL) artisan db:seed

dev: ## Vite em modo dev
	$(SAIL) npm run dev

shell: ## Abre um shell no container da aplicação
	$(SAIL) shell

logs: ## Acompanha os logs dos containers
	$(SAIL) logs -f

lint: ## Formata o código com Pint
	$(SAIL) composer lint

test: ## Roda os testes
	$(SAIL) artisan test
