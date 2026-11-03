SAIL = ./vendor/bin/sail
.PHONY: git up down npm-check

git:
	git add .
	git commit -m "$m"

up: ## Démarre les conteneurs
	$(SAIL) up -d

down: ## Arrête les conteneurs
	$(SAIL) down

hr: ## Lance le npm hot reload
	$(SAIL) pnpm run dev

ide-helper: ## Génere les helpers pour ide phpstorm
	$(SAIL) artisan ide-helper:generate
	$(SAIL) artisan ide-helper:models --nowrite

pnpm-check: ## Check les fichiers
	$(SAIL) pnpm run check

sanity-php: ## Validation du code
	$(SAIL) bin phpstan analyse

sanity-pint: ## Corrige la qualité du code
	$(SAIL) bin pint --dirty

sanity-js: ## Vérifie les js
	$(SAIL) pnpm run check:fix
	$(SAIL) pnpm run types:check

route: ## Générate routes
	$(SAIL) artisan wayfinder:generate --with-form
