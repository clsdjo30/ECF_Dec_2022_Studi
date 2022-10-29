# Lions Fitness Club - ECF 2022

## Demo

Une démonstration du projet est disponible - [Lions Fitness Club](https://ecf-studi-2022.herokuapp.com/)

Pour tester l'application en technicien :

````
id : technicien@fitness-club.fr
mdp: password
````

Pour tester l'application en Franchisé :

````
id : franchise@fitness-club.fr
mdp: password
````

Pour tester l'application en Manager de salle :

````
id : manager@fitness-club.fr
mdp: password
````

## Installation en local

Pour installer le projet en local, rendez-vous dans votre dossier d'installation

```bash
 git clone <link to repository>
```

Une fois cloner, ouvrez votre IDE

```bash
composer install
npm install
npm run build
```

Creer la BDD dans votre .env

```bash
 DATABASE_URL="mysql://<ID de bdd>:<Mdp de Bdd>@127.0.0.1:3306/<Nom de votre BDD>?serverVersion=<Version de votre BDD> &charset=utf8mb4"

```

Dans le terminal

```bash
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migration:migrate
```

Lancez le serveur Symfony

```bash
symfony serve -d
```

Vous pouvez installer des données de test
Lancez le serveur Symfony

```bash
php bin/console doctrine:fixture:load
```

