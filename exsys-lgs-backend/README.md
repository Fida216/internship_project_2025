# Exsys-LGS-Backend

Backend API pour le système Exsys-LGS développé avec Symfony.

## Prérequis

- PHP 8.1+
- Composer
- SQL Server 2016+
- Extensions PHP : `pdo_sqlsrv`, `sqlsrv`

## Installation

1. **Cloner le projet**
```bash
git clone https://gitlab.com/exsys-lgs/exsys-lgs-backend.git
cd exsys-lgs-backend
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configuration de la base de données**
   - Modifier le fichier `.env` avec vos paramètres SQL Server :
```env
DATABASE_URL="sqlsrv://sa:admin@localhost:1433/exsys"
```

## Configuration de la base de données

### Créer la base de données
```bash
php bin/console doctrine:database:create
```

### Générer et appliquer les migrations
```bash
# Générer une migration à partir des entités
php bin/console make:migration

# Appliquer les migrations
php bin/console doctrine:migrations:migrate
```

### Commandes alternatives (développement uniquement)
```bash
# Créer le schéma directement (sans migrations)
php bin/console doctrine:schema:create

# Mettre à jour le schéma existant
php bin/console doctrine:schema:update --force
```

## Configuration de l'IA (Ollama)

Le système utilise Ollama pour la génération et l'amélioration de messages marketing via l'intelligence artificielle.

### Installation d'Ollama

1. **Télécharger et installer Ollama**
   - Visitez [https://ollama.ai](https://ollama.ai)
   - Téléchargez et installez la version pour votre système d'exploitation
   - Ou utilisez les commandes suivantes :

   **Windows :**
   ```powershell
   # Télécharger depuis le site officiel ou utiliser winget
   winget install Ollama.Ollama
   ```

   **Linux :**
   ```bash
   curl -fsSL https://ollama.ai/install.sh | sh
   ```

   **macOS :**
   ```bash
   # Via Homebrew
   brew install ollama
   ```

2. **Démarrer le service Ollama**
   ```bash
   ollama serve
   ```

3. **Télécharger le modèle**
   ollama pull gemma3:1b
   ```

### Configuration

1. **Variables d'environnement**
   
   Le fichier `.env` contient la configuration Ollama :
   ```env
   ###> Ollama Configuration ###
   OLLAMA_BASE_URL=http://localhost:11434
   OLLAMA_DEFAULT_MODEL=gemma3:1b
   ###< Ollama Configuration ###
   ```

## Lancement du serveur

### Serveur de développement Symfony
```bash
# Démarrer le serveur local
symfony server:start

# Ou avec PHP built-in server
php -S localhost:8000 -t public/
```

### Avec Docker (optionnel)
```bash
# Démarrer avec Docker Compose
docker-compose up -d

# Arrêter les services
docker-compose down
```

## Documentation API

L'API dispose d'une documentation interactive générée automatiquement avec Swagger/OpenAPI.

### Accès à la documentation

Une fois le serveur démarré, la documentation est accessible aux adresses suivantes :

- **Interface Swagger UI** : [http://localhost:8000/api/doc](http://localhost:8000/api/doc)

### Collection Postman

Une collection Postman complète est disponible pour tester l'API :

- **Fichier** : `ExSys-API-Complete.postman_collection.json`
- **Import dans Postman** :
  1. Ouvrir Postman
  2. Cliquer sur "Import"
  3. Sélectionner le fichier `ExSys-API-Complete.postman_collection.json`
  4. La collection sera automatiquement importée avec tous les endpoints

### Fonctionnalités

- Documentation interactive de tous les endpoints
- Tests directs depuis l'interface
- Schémas des modèles de données
- Exemples de requêtes et réponses
- Collection Postman prête à l'emploi pour les tests

## Commandes utiles

### Vérifications
```bash
# Vérifier la configuration Doctrine
php bin/console doctrine:mapping:info

# Valider le schéma de base de données
php bin/console doctrine:schema:validate

# Lister les migrations
php bin/console doctrine:migrations:status
```

### Développement
```bash
# Générer une nouvelle entité
php bin/console make:entity

# Générer un contrôleur
php bin/console make:controller


## Structure du projet

```
src/
├── Kernel.php                  # Noyau Symfony
├── Domain/                     # Domaines métier
│   ├── Auth/                   # Authentification
│   │   ├── Controller/         # Contrôleurs d'authentification
│   │   ├── DTO/               # Data Transfer Objects
│   │   ├── Mapper/            # Mappers DTO ↔ Entité
│   │   └── Service/           # Services métier Auth
│   ├── Client/                # Gestion des clients
│   ├── ExchangeOffice/        # Bureaux de change
│   └── User/                  # Gestion des utilisateurs
│
└── Shared/                    # Éléments partagés
    ├── Controller/            # Contrôleurs abstraits
    ├── Enum/                  # Énumérations globales
    ├── EventListener/         # Écouteurs d'événements
    └── Service/               # Services transversaux

config/                        # Configuration Symfony
├── packages/                  # Configuration des bundles
└── routes/                    # Configuration des routes

migrations/                    # Migrations de base de données
public/                        # Point d'entrée web
```

