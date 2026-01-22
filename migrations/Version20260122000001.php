<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260122000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create contracts table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contracts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            contract_number VARCHAR(50) NOT NULL UNIQUE,
            contract_date DATETIME NOT NULL,
            contract_value NUMERIC(12, 2) NOT NULL,
            payment_method VARCHAR(20) NOT NULL,
            client_name VARCHAR(100),
            description TEXT,
            created_at DATETIME NOT NULL,
            updated_at DATETIME,
            status VARCHAR(20) NOT NULL DEFAULT \'PENDING\'
        )');

        $this->addSql('CREATE INDEX idx_contract_number ON contracts(contract_number)');
        $this->addSql('CREATE INDEX idx_payment_method ON contracts(payment_method)');
        $this->addSql('CREATE INDEX idx_status ON contracts(status)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE contracts');
    }
}
