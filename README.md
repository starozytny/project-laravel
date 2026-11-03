# Project Laravel

Base de projet Laravel + Inertia (React), prête à être clonée et lancée sur n'importe quelle machine grâce à Docker. Elle sert de point de départ pour d'autres projets.

## Ce que contient le projet

- **Pile technique** : Laravel 13, Inertia v3 + React 19, Vite+ (`vp`), Tailwind CSS v4, shadcn/ui, PostgreSQL 18, [Laravel Sail](https://laravel.com/docs/sail).
- **Authentification** (Fortify) : connexion, mot de passe oublié, double authentification, passkeys, paramètres du profil.
- **Rôles** (Spatie Permission) : `developer`, `admin`, `user`, avec une hiérarchie (un `developer` a les droits d'un `admin`, qui a ceux d'un `user`).
- **Trois espaces séparés**, chacun avec son layout : public, membre (`/espace-membre`) et administration (`/admin`).
- **Administration** : gestion des utilisateurs (liste, recherche, filtre par rôle, création, modification, suppression) et des messages reçus par le formulaire de contact.
- **Pages publiques** : accueil, contact (avec mention RGPD et antispam), mentions légales, politique de confidentialité. Les textes légaux sont des modèles à adapter à chaque projet.
- **E-mails** : notification à l'administrateur à chaque message de contact, testable en local avec [Mailpit](https://mailpit.axllent.org/).

## Prérequis

Le seul prérequis pour lancer le projet est **Docker Desktop** avec l'intégration WSL2 activée (si tu es sous Windows/WSL). Pas besoin de PHP, de Composer ni de Node installés sur la machine hôte : tout tourne dans des conteneurs via [Laravel Sail](https://laravel.com/docs/sail).

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

Cela build l'image de l'app (PHP 8.5, voir `compose.yaml`) et démarre les conteneurs : l'application, PostgreSQL et Mailpit.

Puis, dans l'ordre :

```bash
sail artisan key:generate
sail artisan migrate --seed
sail pnpm install
sail pnpm run dev   # ou: make hr
```

`migrate --seed` crée les tables puis les rôles (`developer`, `admin`, `user`).

L'app est accessible sur `APP_URL` (`http://localhost:8000` par défaut — le port doit correspondre à `APP_PORT` dans `.env`, qui vaut `80` par défaut dans `compose.yaml` si non précisé).

> Le projet utilise **pnpm** (voir `pnpm-lock.yaml`). Ne lance pas `npm install` : il génèrerait un `package-lock.json` en plus et pourrait installer d'autres versions. Les outils JavaScript (`vp`) demandent Node 22.18 ou plus : ils tournent dans le conteneur Sail, passe toujours par `sail pnpm ...`.

### Routes TypeScript (Wayfinder)

Les fichiers `resources/js/routes`, `actions` et `wayfinder` sont générés à partir des routes Laravel et ne sont pas versionnés. Ils sont créés au lancement de `pnpm run dev` ou `pnpm run build`. Pour les régénérer à la main (par exemple avant `types:check`, ou après avoir ajouté une route) :

```bash
sail artisan wayfinder:generate --with-form   # ou: make route
```

## Initialisation des données

Crée un utilisateur pour pouvoir te connecter (commande interactive : demande e-mail, nom, rôle(s) et mot de passe) :

```bash
sail artisan user:create
```

Tu peux pré-remplir nom/email/rôle(s) en option (le mot de passe est toujours demandé de façon interactive) :

```bash
sail artisan user:create --name="Admin" --email="admin@example.com" --role=admin
```

Rôles disponibles : `developer`, `admin`, `user`. Sans rôle précisé, l'utilisateur reçoit `user`.

## Espaces et pages

| Adresse | Page | Accès |
|---|---|---|
| `/` | Accueil | public |
| `/contact` | Formulaire de contact | public |
| `/mentions-legales`, `/politique-de-confidentialite` | Pages légales | public |
| `/login` | Connexion | visiteurs |
| `/espace-membre` | Espace membre | utilisateur connecté |
| `/admin` | Tableau de bord administrateur | `admin` et `developer` |
| `/admin/utilisateurs` | Gestion des utilisateurs | `admin` et `developer` |
| `/admin/messages` | Messages du formulaire de contact | `admin` et `developer` |

Après connexion, un administrateur est redirigé vers `/admin`, un utilisateur simple vers `/espace-membre`. Le front ne connaît pas les noms de rôles : il lit les droits partagés par le serveur (`auth.abilities.accessAdmin`).

## E-mails en local (Mailpit)

Le conteneur `mailpit` capture tous les e-mails envoyés par l'application, sans rien livrer. L'interface web est sur **http://localhost:8025**.

Les variables de `.env.example` sont déjà configurées pour lui :

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
CONTACT_RECIPIENT="hello@example.com"
```

- `CONTACT_RECIPIENT` est l'adresse qui reçoit les messages du formulaire de contact. Mets la tienne dans ton `.env` (pas dans `.env.example`, qui est versionné).
- Le lien « Ouvrir dans l'administration » des e-mails est construit à partir de `APP_URL` : il doit correspondre à l'adresse que tu utilises dans le navigateur.
- Pour tester « mot de passe oublié » ou le formulaire de contact : fais l'action, puis ouvre Mailpit.

Si les ports 1025 ou 8025 sont déjà utilisés sur ta machine, change `FORWARD_MAILPIT_PORT` et `FORWARD_MAILPIT_DASHBOARD_PORT` dans `.env`.

## Qualité du code

Les commandes du `makefile` (`make` à la racine) enchaînent les vérifications :

| Commande | Rôle |
|---|---|
| `make sanity-pint` | Formate le PHP (Pint) |
| `make sanity-php` | Analyse statique PHP (PHPStan / Larastan) |
| `make sanity-js` | Formate et vérifie le front, puis les types TypeScript |
| `make pnpm-check` | Vérifie le front sans rien modifier |
| `make ide-helper` | Génère les fichiers d'aide pour PhpStorm |

Tests (Pest) :

```bash
sail artisan test --compact
sail artisan test --compact tests/Feature/Admin   # un dossier ou un fichier seulement
```

## Personnaliser en local

Un fichier `compose.override.yaml` à la racine est fusionné automatiquement avec `compose.yaml`, et il est ignoré par git. Il sert à adapter le projet à ta machine sans toucher au dépôt (par exemple rattacher l'application à un réseau Docker existant ou utiliser un Mailpit partagé). Pense à mettre les fins de ligne en LF (Unix) dans ton éditeur.

## Commandes utiles

```bash
sail up -d          # démarre les conteneurs (ou: make up)
sail down           # les arrête (ou: make down)
sail down -v        # supprime aussi les volumes (reset complet de la DB)
sail artisan migrate
sail artisan route:list --name=admin
sail pnpm run build # compile les assets pour la production
```

## Dépannage

- **`Docker or Podman is not running` alors que Docker tourne** : ton shell n'a pas encore pris en compte l'ajout au groupe `docker` (`usermod -aG docker $USER`). Lance `newgrp docker` ou ouvre un nouveau terminal.
- **`failed to resolve reference "sail-8.5/app:latest"` au build** : Docker essaie de pull l'image au lieu de la builder. Lance `sail build --no-cache` puis `sail up -d`.
- **`password authentication failed for user "sail"`** : le volume Postgres a été initialisé avec d'anciennes variables d'env. Fais `sail down -v` (supprime le volume) puis `sail up -d` pour le réinitialiser avec les valeurs actuelles de `.env`.
- **Site inaccessible sur `localhost:8000`** : vérifie que `APP_PORT` dans `.env` correspond bien au port que tu utilises (par défaut dans `compose.yaml`, le port exposé est `80` si `APP_PORT` n'est pas défini). Si une autre application occupe déjà ce port, tu verras ses pages (ou ses erreurs) à la place des tiennes.
- **`MissingAppKeyException`** : `APP_KEY` est vide dans `.env` → lance `sail artisan key:generate`.
- **`sh: 1: vp: not found`** (sur `pnpm run dev`) : `node_modules` n'est pas installé → lance `sail pnpm install`.
- **Erreur `Expected @voidzero-dev/vite-plus-core@... but found vite@...`** : les versions de `vite-plus` et de l'alias `vite` dans `package.json` ne sont plus identiques. Elles doivent toujours être mises à jour ensemble.
- **Les e-mails n'arrivent pas dans Mailpit** : vérifie `MAIL_HOST=mailpit` et `MAIL_PORT=1025` dans `.env`, puis `sail artisan config:clear`. Une erreur d'envoi n'empêche pas l'enregistrement d'un message de contact : elle est consignée dans `storage/logs`.
- **Imports `@/routes/...` introuvables (TypeScript ou IDE)** : les routes générées n'existent pas encore → `sail artisan wayfinder:generate --with-form`. Dans PhpStorm, ces dossiers ne doivent pas être exclus du projet.
