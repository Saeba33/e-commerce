<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250827142000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove city_id foreign key from order table, keep only cityName and shippingCost';
    }

    public function up(Schema $schema): void
    {
        // Supprimer la contrainte de clé étrangère et la colonne city_id
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993988BAC62AF');
        $this->addSql('DROP INDEX IDX_F52993988BAC62AF ON `order`');
        $this->addSql('ALTER TABLE `order` DROP city_id');
    }

    public function down(Schema $schema): void
    {
        // Recréer la colonne et la contrainte (attention aux données existantes)
        $this->addSql('ALTER TABLE `order` ADD city_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993988BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_F52993988BAC62AF ON `order` (city_id)');
    }
}
