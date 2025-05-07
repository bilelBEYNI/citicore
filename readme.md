Projet développé dans le cadre du module Projet Intégré : Développement Web__3A26
à Esprit School of Engineering, année universitaire 2024–2025.

#Présentation
CitiCore est une application web Symfony dédiée à la gestion centralisée des services urbains dans une ville intelligente.
Elle permet aux citoyens et aux administrateurs de :

Déposer et suivre des réclamations

Participer à des événements

Acheter des produits via un marketplace

Contribuer à des projets de dons

Échanger via un système de messagerie intégré

Suivre des statistiques en temps réel sur les activités

Côté administration, CitiCore fournit des interfaces puissantes pour gérer les modules, répondre aux citoyens, et générer des rapports décisionnels.


#Modules Fonctionnels
Utilisateur
Inscription, connexion

Gestion du profil (Cin_Utilisateur)

Attribution de rôles (admin, citoyen, organisateur)

Système d’expérience ou retour d’usage

Réclamation / Réponse
Ajout, modification, suppression, affichage par statut

Réclamations liées au CIN de l’utilisateur connecté

Réponses filtrées par utilisateur + boutons d’action dédiés

Interface combinée Réclamation/Réponse

ComboBox dynamique pour le type de réclamation

Marketplace
Vente de produits

Gestion des commandes et suivis

Historique des actions (ajout, suppression, modification)

Donation
Projets de dons + interface donateur

Suivi des contributions 


Événement
Création et participation à des événements caritatifs

Notifications avant les événements

Statistiques de participation

Communication
Messagerie interne : citoyen ↔ organisateur

Notifications SMS et Email selon statut

#Fonctionnalités Avancées
Statistiques temps réel (réclamations traitées, rejetées, en attente)

Historique détaillé des actions utilisateur

Génération automatique de rapports PDF

Affichage dynamique des réponses/réclamations

Paiement sécurisé via intégration externe possible (Paymee, Stripe…)

Génération de PDF et QR Code pour événements ou reçus

Notifications par e-mail ou SMS (Brevo, Twilio)

Tableau de bord avec statistiques dynamiques : satisfaction, projets en cours, etc.

Design moderne avec CSS animé et composants JavaScript/Twig dynamiques


#Technologies Utilisées
Symfony 6.4, PHP 8.1

Doctrine ORM, Twig, Bootstrap

JavaScript (Stimulus + modules dynamiques)

Chart.js, Google Charts

Endroid QRCode, KnpPaginator

JavaFX pour version desktop (complémentaire)

#Services & APIs Intégrés (src/Service/)

Service/API	Fonction
SendGrid	Envoi d’e-mails automatiques
Infobip (ou Twilio gratuit)	SMS de confirmation et notification
SmsController	Liaison Réclamation ↔ Notification
EmailController	Liaison Réponse ↔ Notification + SendGrid
QrCodeService	Génération de QR Codes
ReclamationService	Gestion de ComboBox, filtres, priorités
StatistiquesService	Graphiques et suivis temps réel


#Topics
symfony
php
smartcity
citoyen
réclamation
réponse
admin-dashboard
notification
sms
don
événement
marketplace

#Structure du Projet

CitiCore/
├── src/
│   ├── Controller/
│   ├── Entity/
│   ├── Form/
│   └── Service/
├── templates/
├── public/
├── config/
├── migrations/
├── .env
└── composer.json

#Installation

git clone https://github.com/tonprofil/CitiCore.git
cd CitiCore
composer install

Configure la base de données dans le fichier .env :
DATABASE_URL="mysql://user:password@127.0.0.1:3306/citicore_db"

Puis lance les commandes suivantes :
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony serve


#Statistiques Intégrées
Réclamations par statut (Graphiques ligne + barres)

Utilisateurs actifs par module (Histogrammes)


#Contribution
Fork du projet

Créer une branche :
git checkout -b feature/ma-feature

Commit :
git commit -m "Ajout fonctionnalité"

Push :
git push origin feature/ma-feature

Ouvre une Pull Request sur GitHub

#Remerciements
Projet réalisé sous la supervision de l’équipe pédagogique
Esprit School of Engineering – Module Projet Intégré : Développement Web__3A26

"Une ville intelligente commence par une interface intelligente."

