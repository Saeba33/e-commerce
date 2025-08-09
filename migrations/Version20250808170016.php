<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250808170016 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_products DROP FOREIGN KEY FK_5242B8EB4584665A');
        $this->addSql('ALTER TABLE product_history DROP FOREIGN KEY FK_F6636BFB4584665A');
        
        $this->addSql('ALTER TABLE order_products CHANGE product_id product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_history CHANGE product_id product_id INT DEFAULT NULL');
        
        $this->addSql('ALTER TABLE order_products ADD CONSTRAINT FK_5242B8EB4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE product_history ADD CONSTRAINT FK_F6636BFB4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_products DROP FOREIGN KEY FK_5242B8EB4584665A');
        $this->addSql('ALTER TABLE product_history DROP FOREIGN KEY FK_F6636BFB4584665A');
        
        $this->addSql('ALTER TABLE product_history CHANGE product_id product_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_products CHANGE product_id product_id INT NOT NULL');
        
        $this->addSql('ALTER TABLE order_products ADD CONSTRAINT FK_5242B8EB4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_history ADD CONSTRAINT FK_F6636BFB4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }
}
