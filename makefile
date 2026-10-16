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
	$(SAIL) npm run dev

ide-helper: ## Génere les helpers pour ide phpstorm
	$(SAIL) artisan ide-helper:generate
	$(SAIL) artisan ide-helper:models --nowrite

npm-check: ## Check les fichiers
	$(SAIL) npm run check

php-sanity: ## Validation du code
	$(SAIL) bin phpstan analyse

pint-sanity: ## Corrige la qualité du code
	$(SAIL) bin pint --dirty
