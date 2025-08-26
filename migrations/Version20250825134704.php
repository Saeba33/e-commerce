<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250825134704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename product_history index to product_stock_history';
    }

    public function up(Schema $schema): void
    {
        // Renommage de l'index pour correspondre à la nouvelle entité
        $this->addSql('ALTER TABLE product_stock_history RENAME INDEX idx_f6636bfb4584665a TO IDX_E4F17C1A4584665A');
    }

    public function down(Schema $schema): void
    {
        // Retour à l'ancien nom d'index
        $this->addSql('ALTER TABLE product_stock_history RENAME INDEX IDX_E4F17C1A4584665A TO idx_f6636bfb4584665a');
        $this->addSql('ALTER TABLE product_stock_history RENAME INDEX idx_e4f17c1a4584665a TO IDX_F6636BFB4584665A');
    }
}
