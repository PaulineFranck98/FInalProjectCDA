<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251106101424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_itinerary (user_id INT NOT NULL, itinerary_id INT NOT NULL, INDEX IDX_FFC2B512A76ED395 (user_id), INDEX IDX_FFC2B51215F737B2 (itinerary_id), PRIMARY KEY(user_id, itinerary_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_itinerary ADD CONSTRAINT FK_FFC2B512A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_itinerary ADD CONSTRAINT FK_FFC2B51215F737B2 FOREIGN KEY (itinerary_id) REFERENCES itinerary (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_itinerary DROP FOREIGN KEY FK_FFC2B512A76ED395');
        $this->addSql('ALTER TABLE user_itinerary DROP FOREIGN KEY FK_FFC2B51215F737B2');
        $this->addSql('DROP TABLE user_itinerary');
    }
}
