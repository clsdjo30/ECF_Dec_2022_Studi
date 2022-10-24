<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221024132106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subsidiary_permission (id INT AUTO_INCREMENT NOT NULL, subsidiary_id INT NOT NULL, is_active TINYINT(1) DEFAULT NULL, INDEX IDX_9B3AECF9D4A7BDA2 (subsidiary_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subsidiary_permission ADD CONSTRAINT FK_9B3AECF9D4A7BDA2 FOREIGN KEY (subsidiary_id) REFERENCES subsidiary (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subsidiary_permission DROP FOREIGN KEY FK_9B3AECF9D4A7BDA2');
        $this->addSql('DROP TABLE subsidiary_permission');
    }
}
