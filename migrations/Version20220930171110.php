<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20220930171110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create relation with Permission';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subsidiary_permission (subsidiary_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_9B3AECF9D4A7BDA2 (subsidiary_id), INDEX IDX_9B3AECF9FED90CCA (permission_id), PRIMARY KEY(subsidiary_id, permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subsidiary_permission ADD CONSTRAINT FK_9B3AECF9D4A7BDA2 FOREIGN KEY (subsidiary_id) REFERENCES subsidiary (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subsidiary_permission ADD CONSTRAINT FK_9B3AECF9FED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subsidiary_permission DROP FOREIGN KEY FK_9B3AECF9D4A7BDA2');
        $this->addSql('ALTER TABLE subsidiary_permission DROP FOREIGN KEY FK_9B3AECF9FED90CCA');
        $this->addSql('DROP TABLE subsidiary_permission');
    }
}
