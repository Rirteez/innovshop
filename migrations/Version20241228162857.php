<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241228162857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_item CHANGE variant variant VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE ligne_facture ADD quantity INT NOT NULL, ADD unit_price NUMERIC(10, 2) NOT NULL, ADD variant VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ligne_facture DROP quantity, DROP unit_price, DROP variant');
        $this->addSql('ALTER TABLE cart_item CHANGE variant variant VARCHAR(255) DEFAULT NULL');
    }
}
