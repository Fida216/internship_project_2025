<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250728104003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modify client table to use country relation instead of nationality enum';
    }

    public function up(Schema $schema): void
    {
        // Add country_id column to client table
        $this->addSql('ALTER TABLE client ADD country_id UNIQUEIDENTIFIER');
        
        // Add foreign key constraint
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        
        // Create index on country_id
        $this->addSql('CREATE INDEX IDX_C7440455F92F3E70 ON client (country_id)');
    }

    public function down(Schema $schema): void
    {
        // Remove foreign key constraint and index
        $this->addSql('ALTER TABLE client DROP CONSTRAINT FK_C7440455F92F3E70');
        $this->addSql('DROP INDEX IDX_C7440455F92F3E70 ON client');
        
        // Remove country_id column
        $this->addSql('ALTER TABLE client DROP COLUMN country_id');
    }
}
