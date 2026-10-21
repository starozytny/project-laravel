# Project Laravel

## Prérequis

Le seul prérequis pour lancer le projet est **Docker Desktop** avec l'intégration WSL2 activée (si tu es sous Windows/WSL). Pas besoin de PHP ni de Composer installés sur la machine hôte : tout tourne dans des conteneurs via [Laravel Sail](https://laravel.com/docs/sail).

- Docker Desktop → Settings → Resources → WSL Integration → active le toggle pour ta distro.
- (Optionnel mais recommandé) Ajoute ton utilisateur au groupe `docker` pour éviter de préfixer chaque commande par `sudo` :
    ```bash
    sudo usermod -aG docker $USER
    ```
    Puis redémarre ton terminal (ou `newgrp docker`).

## Installation (première fois)

Le dossier `vendor/` n'existe pas encore dans un clone frais, donc `vendor/bin/sail` n'est pas disponible. On bootstrap avec une image Composer temporaire :

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

> Le projet nécessite PHP `^8.3`. L'image `laravelsail/php84-composer` est utilisée ici car il n'existe pas encore d'image Composer officielle `php85-composer` au moment de l'écriture — elle sert uniquement à générer `vendor/`, pas à faire tourner l'app.

Copie ensuite le fichier d'environnement :

```bash
cp .env.example .env
```

## Alias sail (optionnel)

Pour éviter de taper `./vendor/bin/sail` à chaque commande, ajoute cet alias dans ton `~/.bashrc` (ou `~/.zshrc`) :

```bash
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

Puis recharge ton terminal (`source ~/.bashrc`). Tu peux ensuite utiliser `sail up -d`, `sail artisan ...`, etc. directement, tant que tu es dans le dossier racine du projet. (Les commandes ci-dessous utilisent `sail`, remplace par `./vendor/bin/sail` si tu n'as pas configuré l'alias.)

## Lancer le projet

```bash
sail up -d
```

Cela build l'image de l'app (PHP 8.5, voir `compose.yaml`) et démarre les conteneurs (app + PostgreSQL).

Puis, dans l'ordre :

```bash
sail artisan key:generate
sail artisan migrate
sail pnpm install
sail npm run dev   # ou: make hr
```

L'app est accessible sur `APP_URL` (`http://localhost:8000` par défaut — le port doit correspondre à `APP_PORT` dans `.env`, qui vaut `80` par défaut dans `compose.yaml` si non précisé).

## Initialisation des données

Lance les seeders (rôles, etc.) :

```bash
sail artisan db:seed
```

Puis crée un utilisateur pour pouvoir te connecter (commande interactive : demande e-mail, nom, rôle(s) et mot de passe) :

```bash
sail artisan user:create
```

Tu peux pré-remplir nom/email/rôle(s) en option (le mot de passe est toujours demandé de façon interactive) :

```bash
sail artisan user:create --name="Admin" --email="admin@example.com" --role=admin
```

Rôles disponibles : `developer`, `admin`, `user`.

## Commandes utiles

```bash
sail artisan migrate
sail artisan key:generate
sail down
sail down -v   # supprime aussi les volumes (ex: reset complet de la DB)
```

## Dépannage

- **`Docker or Podman is not running` alors que Docker tourne** : ton shell n'a pas encore pris en compte l'ajout au groupe `docker` (`usermod -aG docker $USER`). Lance `newgrp docker` ou ouvre un nouveau terminal.
- **`failed to resolve reference "sail-8.5/app:latest"` au build** : Docker essaie de pull l'image au lieu de la builder. Lance `sail build --no-cache` puis `sail up -d`.
- **`password authentication failed for user "sail"`** : le volume Postgres a été initialisé avec d'anciennes variables d'env. Fais `sail down -v` (supprime le volume) puis `sail up -d` pour le réinitialiser avec les valeurs actuelles de `.env`.
- **Site inaccessible sur `localhost:8000`** : vérifie que `APP_PORT` dans `.env` correspond bien au port que tu utilises (par défaut dans `compose.yaml`, le port exposé est `80` si `APP_PORT` n'est pas défini).
- **`MissingAppKeyException`** : `APP_KEY` est vide dans `.env` → lance `sail artisan key:generate`.
- **`sh: 1: vp: not found`** (sur `npm run dev`) : `node_modules` n'est pas installé → lance `sail pnpm install` (le projet utilise `pnpm`, voir `pnpm-lock.yaml` — ne pas utiliser `npm install`, qui génère un `package-lock.json` en plus et peut installer des versions différentes).
