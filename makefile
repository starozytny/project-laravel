SAIL = ./vendor/bin/sail
.PHONY: git up down npm-check

git:
	git add .
	git commit -m "$m"

up: ## Démarre les conteneurs
	$(SAIL) up -d

down: ## Arrête les conteneurs
	$(SAIL) down

npm-check: ## Check les fichiers
	$(SAIL) npm run check
