<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250827120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename order columns pay_on_delivery -> is_pickup and is_completed -> is_delivered';
    }

    public function up(Schema $schema): void
    {
        // MySQL: rename columns
        $this->addSql("ALTER TABLE `order` CHANGE COLUMN `pay_on_delivery` `is_pickup` TINYINT(1) DEFAULT NULL");
        $this->addSql("ALTER TABLE `order` CHANGE COLUMN `is_completed` `is_delivered` TINYINT(1) DEFAULT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE `order` CHANGE COLUMN `is_pickup` `pay_on_delivery` TINYINT(1) DEFAULT NULL");
        $this->addSql("ALTER TABLE `order` CHANGE COLUMN `is_delivered` `is_completed` TINYINT(1) DEFAULT NULL");
    }
}
