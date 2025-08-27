<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250827141000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add city_name to order for invoice snapshot';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `order` ADD city_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `order` DROP city_name');
    }
}
