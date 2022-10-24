<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221024134933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subsidiary_permission ADD partner_permission_id INT NOT NULL');
        $this->addSql('ALTER TABLE subsidiary_permission ADD CONSTRAINT FK_9B3AECF9B221318B FOREIGN KEY (partner_permission_id) REFERENCES partner_permission (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9B3AECF9B221318B ON subsidiary_permission (partner_permission_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subsidiary_permission DROP FOREIGN KEY FK_9B3AECF9B221318B');
        $this->addSql('DROP INDEX UNIQ_9B3AECF9B221318B ON subsidiary_permission');
        $this->addSql('ALTER TABLE subsidiary_permission DROP partner_permission_id');
    }
}
