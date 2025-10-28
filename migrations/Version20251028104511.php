<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251028104511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE itinerary (id INT AUTO_INCREMENT NOT NULL, itinerary_name VARCHAR(255) NOT NULL, duration INT DEFAULT NULL, creation_date DATETIME NOT NULL, is_public TINYINT(1) NOT NULL, departure_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE itinerary_user (itinerary_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8AB0BC1D15F737B2 (itinerary_id), INDEX IDX_8AB0BC1DA76ED395 (user_id), PRIMARY KEY(itinerary_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE itinerary_location (id INT AUTO_INCREMENT NOT NULL, itinerary_id INT NOT NULL, location_id VARCHAR(255) NOT NULL, order_index INT NOT NULL, INDEX IDX_7F7CBD0A15F737B2 (itinerary_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE itinerary_user ADD CONSTRAINT FK_8AB0BC1D15F737B2 FOREIGN KEY (itinerary_id) REFERENCES itinerary (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE itinerary_user ADD CONSTRAINT FK_8AB0BC1DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE itinerary_location ADD CONSTRAINT FK_7F7CBD0A15F737B2 FOREIGN KEY (itinerary_id) REFERENCES itinerary (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE itinerary_user DROP FOREIGN KEY FK_8AB0BC1D15F737B2');
        $this->addSql('ALTER TABLE itinerary_user DROP FOREIGN KEY FK_8AB0BC1DA76ED395');
        $this->addSql('ALTER TABLE itinerary_location DROP FOREIGN KEY FK_7F7CBD0A15F737B2');
        $this->addSql('DROP TABLE itinerary');
        $this->addSql('DROP TABLE itinerary_user');
        $this->addSql('DROP TABLE itinerary_location');
    }
}
