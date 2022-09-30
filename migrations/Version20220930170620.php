<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20220930170620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Subsidiary Entity & Add relation with User & Partner Entity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subsidiary (id INT AUTO_INCREMENT NOT NULL, partner_id INT NOT NULL, name VARCHAR(255) NOT NULL, logo_url VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, phone_number VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postal_code INT NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_732CF96A9393F8FE (partner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subsidiary ADD CONSTRAINT FK_732CF96A9393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE user ADD room_manager_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494CCC447F FOREIGN KEY (room_manager_id) REFERENCES subsidiary (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6494CCC447F ON user (room_manager_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494CCC447F');
        $this->addSql('ALTER TABLE subsidiary DROP FOREIGN KEY FK_732CF96A9393F8FE');
        $this->addSql('DROP TABLE subsidiary');
        $this->addSql('DROP INDEX UNIQ_8D93D6494CCC447F ON user');
        $this->addSql('ALTER TABLE user DROP room_manager_id');
    }
}
