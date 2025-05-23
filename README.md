# CarExp

  CarExp est un projet réalisé avec Symfony et VueJS afin de vous offrir un site capable de faire de l'achat/revente et location de véhicule.
  À partir d'une simple localisation, vous pouvez trouver le véhicule qui vous correspond le mieux.
  Via notre interface complète, visualisez vos locations et achats/reventes en cours, et même passés.
  Notre site vous permet bien évidemment de créer un compte et de vous y connecter, ainsi que de personnaliser votre profil.

## Table des matières

1. [Fonctionnalités](#fonctionnalités)
2. [Installation](#installation)
3. [Utilisation](#utilisation)

## Fonctionnalités

- Ajouter des véhicules à vendre avec leurs caractéristiques et images.
- Afficher une liste de véhicules disponibles à la vente, avec prochainement la possibilité de filtrer et de rechercher.
- Permettre aux utilisateurs d'acheter des véhicules disponibles à la vente.
- Permettre aux utilisateurs de consulter leur profil, y compris les informations personnelles, les véhicules en vente, les anciennes ventes, les achats et les locations.
- Afficher une liste de véhicules loués par l'utilisateur, avec la possibilité de supprimer une location.
- Améliorer le visuel des pages en ajoutant des styles CSS et des effets interactifs.
- Ajouter des fonctionnalités pour la gestion des ventes, telles que la modification de l'agence de vente et la suppression de ventes.


## Installation

1. Clonez le projet GIT dans votre répertoire local :

    ```bash
    # Avec SSH
    git clone git@gitlab.univ-lr.fr:jbossisg/projet-l2s4.git

    # Avec HTTPS
    git clone https://gitlab.univ-lr.fr/jbossisg/projet-l2s4.git
    ```

2. Assurez-vous d'avoir un serveur de base de données MySQL ou PostgreSQL installé sur votre machine.

3. Créez une base de données pour le projet et configurez les informations de connexion dans le fichier `.env` du projet.

4. Connectez la base de données au projet en configurant les paramètres de connexion dans le fichier `config/packages/doctrine.yaml`.

5. Utilisez un IDE tel que PHPStorm pour exécuter les commandes suivantes :

    - Générez les entités à partir de vos classes PHP :

      ```bash
      php bin/console make:entity
      ```

    - Exécutez les migrations pour mettre à jour la structure de la base de données :

      ```bash
      php bin/console doctrine:migrations:migrate
      ```

    - Chargez-les fixtures pour pré-remplir la base de données avec des données de test :

      ```bash
      php bin/console doctrine:fixtures:load
      ```

6. Une fois ces étapes terminées, vous pouvez lancer le serveur Symfony intégré en exécutant la commande suivante dans votre terminal :

    ```bash
    symfony server:start
    ```
    Si cela ne fonctionne pas, le serveur est peut-être déjà lancé quelque part. Il faudra donc l'éteindre :
    ```bash
    symfony server:stop
    ```
   Puis :
    ```bash
    symfony server:start
    ```

7. Accédez à l'URL indiquée dans la console pour utiliser l'application dans votre navigateur web.

    ```bash
    127.0.0.1:8000/
    ```

## Utilisation

Une fois que vous avez installé le projet et lancé le serveur Symfony, vous pouvez commencer à utiliser l'application. Voici les principales fonctionnalités et comment les utiliser :

### 1. Inscription et Connexion

- Accédez à la page d'inscription en cliquant sur le lien "S'inscrire" sur la page d'accueil.
- Remplissez le formulaire avec vos informations personnelles et cliquez sur "S'inscrire".
- Connectez-vous ensuite en utilisant vos identifiants nouvellement créés.

### 2. Ajout de véhicules à la vente

- Une fois connecté, accédez à la page "Vendre un véhicule" pour ajouter une voiture à la vente.
- Remplissez le formulaire avec les détails de la voiture, y compris l'image, la marque, le modèle, etc.
- Cliquez sur "Ajouter le véhicule" pour publier la voiture à la vente.

### 3. Recherche et achat de véhicules

- Utilisez la barre de navigation pour accéder à la page "Acheter un véhicule".
- Utilisez le formulaire de recherche pour trouver des véhicules disponibles à l'achat.
- Consultez les résultats de la recherche et cliquez sur le bouton "Acheter" pour acheter un véhicule.

### 4. Gestion du profil utilisateur

- Accédez à la page "Profil" pour voir vos informations personnelles, vos véhicules en vente, vos anciennes ventes, vos achats et vos locations.
- Utilisez les options de gestion pour changer l'agence de vente d'un véhicule, supprimer une vente ou une location, etc.

Ces étapes de base vous permettront de démarrer avec l'application. Explorez les différentes fonctionnalités pour une première expérience complète !
Notez que le projet vient de débuter et sera donc amélioré dans le futur.
