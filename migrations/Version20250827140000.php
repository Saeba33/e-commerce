<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250827140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add product_name and current_price to order_products for invoice snapshot';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_products ADD product_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE order_products ADD current_price DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_products DROP product_name');
        $this->addSql('ALTER TABLE order_products DROP current_price');
    }
}
