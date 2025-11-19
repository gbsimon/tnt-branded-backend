# Admin WordPress sur mesure par tom & tom

Un plugin WordPress qui personnalise l'apparence du backend et de la page de connexion avec votre logo et vos couleurs d'accent.

## 📋 Prérequis

- WordPress 5.0 ou supérieur
- PHP 7.4 ou supérieur
- Node.js et npm (pour la compilation SCSS)

## 🚀 Installation

### Installation manuelle

1. Téléchargez ou clonez ce repository
2. Placez le dossier `tnt-branded-backend` dans `/wp-content/plugins/`
3. Activez le plugin depuis le tableau de bord WordPress

### Compilation des styles SCSS

Avant d'utiliser le plugin en production, vous devez compiler les fichiers SCSS :

```bash
npm install
npm run scss:build
```

Cela générera les fichiers CSS minifiés dans `assets/css/`.

## ⚙️ Configuration

1. Allez dans **Réglages > Admin sur mesure par tom & tom**
2. Configurez :
   - **Logo** : Uploadez votre logo personnalisé
   - **Couleur d'accent principale** : Choisissez votre couleur principale
   - **Couleur d'accent secondaire** : Choisissez votre couleur secondaire

Les modifications s'appliquent immédiatement au backend et à la page de connexion.

## 📁 Structure du projet

```
tnt-branded-backend/
├── assets/
│   └── css/          # Fichiers CSS compilés (générés)
├── src/
│   ├── scss/         # Fichiers SCSS source
│   ├── Settings.php  # Gestion des paramètres
│   ├── Styles.php    # Enqueuing des styles
│   ├── LoginPage.php # Personnalisation de la page de connexion
│   ├── AdminPage.php # Page d'administration
│   └── ...
└── tnt-backend.php   # Fichier principal du plugin
```

## 🛠️ Développement

### Compilation en mode développement (avec watch)

```bash
npm run scss:dev
```

### Compilation pour la production

```bash
npm run scss:build
```

## 🔒 Sécurité

- Toutes les entrées utilisateur sont sanitizées
- Toutes les sorties sont échappées
- Vérification des permissions pour toutes les actions admin
- Utilisation des fonctions WordPress natives pour la sécurité

## 📝 Fonctionnalités

- ✅ Personnalisation du logo sur la page de connexion
- ✅ Personnalisation des couleurs d'accent (backend et login)
- ✅ Messages de bienvenue multilingues (FR/EN)
- ✅ Widget de tableau de bord
- ✅ Page de paramètres dédiée
- ✅ Layout personnalisé pour la page de connexion

## 🌐 Support multilingue

Le plugin détecte automatiquement la langue via :
1. Le paramètre URL `wp_lang`
2. La locale WordPress (`get_locale()`)
3. Fallback vers le français

## 📄 Licence

Ce plugin est développé par [tom & tom](https://tomtom.design).

## 🐛 Support

Pour toute question ou problème, contactez [tom & tom](https://tomtom.design).

