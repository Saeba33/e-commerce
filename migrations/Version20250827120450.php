<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250827120450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename city_name column to city in order table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` CHANGE is_pickup is_pickup TINYINT(1) NOT NULL, CHANGE city_name city VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` CHANGE is_pickup is_pickup TINYINT(1) DEFAULT NULL, CHANGE city city_name VARCHAR(255) DEFAULT NULL');
    }
}
