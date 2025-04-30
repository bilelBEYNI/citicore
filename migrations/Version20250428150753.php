<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250428150753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE categorie CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE image_url image_url VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_B26681ED936B2FA ON evenement
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evenement DROP organisateur_id, CHANGE id_evenement id_evenement INT AUTO_INCREMENT NOT NULL, CHANGE date_evenement date_evenement DATETIME DEFAULT NULL, CHANGE lieu_evenement lieu_evenement VARCHAR(255) DEFAULT NULL, CHANGE categorie_id categorie_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evenement ADD CONSTRAINT FK_B26681EBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (categorie_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback CHANGE contenu contenu LONGTEXT NOT NULL, CHANGE Cin_Participant Cin_Participant INT DEFAULT NULL, CHANGE Cin_Organisateur Cin_Organisateur INT DEFAULT NULL, CHANGE date_creation date_creation DATETIME NOT NULL, CHANGE id_FeedBack id_feed_back INT NOT NULL, ADD PRIMARY KEY (id_feed_back)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback ADD CONSTRAINT FK_D2294458AEA02433 FOREIGN KEY (Cin_Participant) REFERENCES utilisateur (Cin) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback ADD CONSTRAINT FK_D22944589ECE13EF FOREIGN KEY (Cin_Organisateur) REFERENCES utilisateur (Cin) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX cin_participant ON feedback
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D2294458AEA02433 ON feedback (Cin_Participant)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX cin_organisateur ON feedback
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D22944589ECE13EF ON feedback (Cin_Organisateur)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit CHANGE id_produit id_produit INT NOT NULL, CHANGE description description LONGTEXT NOT NULL, CHANGE prix prix DOUBLE PRECISION NOT NULL, CHANGE photo photo VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX id_association ON projet_don
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet_don ADD montant_recu DOUBLE PRECISION NOT NULL, DROP montantRecu, CHANGE id_Projet_Don id_projet_don INT NOT NULL, CHANGE objectif objectif DOUBLE PRECISION NOT NULL, CHANGE id_association id_association INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX Cin_Utilisateur ON reclamation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation CHANGE ID_Reclamation id_reclamation INT NOT NULL, CHANGE Description description LONGTEXT NOT NULL, CHANGE Date_Creation date_creation DATETIME NOT NULL, CHANGE Date_Resolution date_resolution DATETIME NOT NULL, CHANGE Type_Reclamation type_reclamation VARCHAR(20) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse DROP FOREIGN KEY idR
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse DROP FOREIGN KEY idR
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse CHANGE ID_Reponse id_reponse INT NOT NULL, CHANGE ID_Reclamation ID_Reclamation INT DEFAULT NULL, CHANGE Contenu contenu LONGTEXT NOT NULL, CHANGE Date_Reponse date_reponse DATETIME NOT NULL, CHANGE Statut statut VARCHAR(20) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC72EF41509 FOREIGN KEY (ID_Reclamation) REFERENCES reclamation (ID_Reclamation) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idr ON reponse
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5FB6DEC72EF41509 ON reponse (ID_Reclamation)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse ADD CONSTRAINT idR FOREIGN KEY (ID_Reclamation) REFERENCES reclamation (ID_Reclamation) ON UPDATE CASCADE ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX Email ON utilisateur
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur CHANGE Role role VARCHAR(255) NOT NULL, CHANGE Token token VARCHAR(255) NOT NULL, CHANGE failed_attempts failed_attempts INT NOT NULL, CHANGE ban_time ban_time DATETIME NOT NULL, CHANGE login_failures login_failures INT NOT NULL, CHANGE is_banned is_banned TINYINT(1) NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE categorie CHANGE description description VARCHAR(255) NOT NULL, CHANGE image_url image_url VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evenement DROP FOREIGN KEY FK_B26681EBCF5E72D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evenement ADD organisateur_id INT DEFAULT NULL, CHANGE id_evenement id_evenement INT NOT NULL, CHANGE categorie_id categorie_id INT DEFAULT NULL, CHANGE date_evenement date_evenement VARCHAR(255) NOT NULL, CHANGE lieu_evenement lieu_evenement VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B26681ED936B2FA ON evenement (organisateur_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback DROP FOREIGN KEY FK_D2294458AEA02433
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback DROP FOREIGN KEY FK_D22944589ECE13EF
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON feedback
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback DROP FOREIGN KEY FK_D2294458AEA02433
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback DROP FOREIGN KEY FK_D22944589ECE13EF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback CHANGE contenu contenu TEXT NOT NULL, CHANGE date_creation date_creation DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE Cin_Participant Cin_Participant INT NOT NULL, CHANGE Cin_Organisateur Cin_Organisateur INT NOT NULL, CHANGE id_feed_back id_FeedBack INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_d2294458aea02433 ON feedback
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX Cin_Participant ON feedback (Cin_Participant)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_d22944589ece13ef ON feedback
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX Cin_Organisateur ON feedback (Cin_Organisateur)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback ADD CONSTRAINT FK_D2294458AEA02433 FOREIGN KEY (Cin_Participant) REFERENCES utilisateur (Cin) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback ADD CONSTRAINT FK_D22944589ECE13EF FOREIGN KEY (Cin_Organisateur) REFERENCES utilisateur (Cin) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit CHANGE id_produit id_produit INT AUTO_INCREMENT NOT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE prix prix NUMERIC(10, 2) NOT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet_don ADD montantRecu NUMERIC(10, 2) NOT NULL, DROP montant_recu, CHANGE id_projet_don id_Projet_Don INT AUTO_INCREMENT NOT NULL, CHANGE objectif objectif NUMERIC(10, 2) NOT NULL, CHANGE id_association id_association INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX id_association ON projet_don (id_association)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation CHANGE id_reclamation ID_Reclamation INT AUTO_INCREMENT NOT NULL, CHANGE description Description TEXT NOT NULL, CHANGE date_creation Date_Creation DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE date_resolution Date_Resolution DATETIME DEFAULT NULL, CHANGE type_reclamation Type_Reclamation VARCHAR(20) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX Cin_Utilisateur ON reclamation (Cin_Utilisateur)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC72EF41509
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC72EF41509
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse CHANGE id_reponse ID_Reponse INT AUTO_INCREMENT NOT NULL, CHANGE contenu Contenu TEXT NOT NULL, CHANGE date_reponse Date_Reponse DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE statut Statut VARCHAR(20) DEFAULT NULL, CHANGE ID_Reclamation ID_Reclamation INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse ADD CONSTRAINT idR FOREIGN KEY (ID_Reclamation) REFERENCES reclamation (ID_Reclamation) ON UPDATE CASCADE ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_5fb6dec72ef41509 ON reponse
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idR ON reponse (ID_Reclamation)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC72EF41509 FOREIGN KEY (ID_Reclamation) REFERENCES reclamation (ID_Reclamation) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur CHANGE role Role VARCHAR(255) DEFAULT 'Participant' NOT NULL, CHANGE token Token VARCHAR(255) DEFAULT NULL, CHANGE failed_attempts failed_attempts INT DEFAULT 0, CHANGE ban_time ban_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE login_failures login_failures INT DEFAULT 0, CHANGE is_banned is_banned TINYINT(1) DEFAULT 0
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX Email ON utilisateur (Email)
        SQL);
    }
}
