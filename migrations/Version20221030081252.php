<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221030081252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE partner (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, phone_number VARCHAR(15) NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partner_permission (id INT AUTO_INCREMENT NOT NULL, partner_id INT DEFAULT NULL, permission_id INT DEFAULT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_FAB8914C9393F8FE (partner_id), INDEX IDX_FAB8914CFED90CCA (permission_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permission (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subsidiary (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, partner_id INT NOT NULL, name VARCHAR(255) NOT NULL, logo_url VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, phone_number VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postal_code INT NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_732CF96AA76ED395 (user_id), INDEX IDX_732CF96A9393F8FE (partner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subsidiary_permission (id INT AUTO_INCREMENT NOT NULL, subsidiary_id INT NOT NULL, partner_permission_id INT NOT NULL, is_active TINYINT(1) DEFAULT NULL, INDEX IDX_9B3AECF9D4A7BDA2 (subsidiary_id), INDEX IDX_9B3AECF9B221318B (partner_permission_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tech_team (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, franchising_id INT DEFAULT NULL, tech_team_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649371E4001 (franchising_id), UNIQUE INDEX UNIQ_8D93D64998773CEB (tech_team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE partner_permission ADD CONSTRAINT FK_FAB8914C9393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE partner_permission ADD CONSTRAINT FK_FAB8914CFED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE subsidiary ADD CONSTRAINT FK_732CF96AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE subsidiary ADD CONSTRAINT FK_732CF96A9393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE subsidiary_permission ADD CONSTRAINT FK_9B3AECF9D4A7BDA2 FOREIGN KEY (subsidiary_id) REFERENCES subsidiary (id)');
        $this->addSql('ALTER TABLE subsidiary_permission ADD CONSTRAINT FK_9B3AECF9B221318B FOREIGN KEY (partner_permission_id) REFERENCES partner_permission (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649371E4001 FOREIGN KEY (franchising_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64998773CEB FOREIGN KEY (tech_team_id) REFERENCES tech_team (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE partner_permission DROP FOREIGN KEY FK_FAB8914C9393F8FE');
        $this->addSql('ALTER TABLE partner_permission DROP FOREIGN KEY FK_FAB8914CFED90CCA');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE subsidiary DROP FOREIGN KEY FK_732CF96AA76ED395');
        $this->addSql('ALTER TABLE subsidiary DROP FOREIGN KEY FK_732CF96A9393F8FE');
        $this->addSql('ALTER TABLE subsidiary_permission DROP FOREIGN KEY FK_9B3AECF9D4A7BDA2');
        $this->addSql('ALTER TABLE subsidiary_permission DROP FOREIGN KEY FK_9B3AECF9B221318B');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649371E4001');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64998773CEB');
        $this->addSql('DROP TABLE partner');
        $this->addSql('DROP TABLE partner_permission');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE subsidiary');
        $this->addSql('DROP TABLE subsidiary_permission');
        $this->addSql('DROP TABLE tech_team');
        $this->addSql('DROP TABLE user');
    }
}
