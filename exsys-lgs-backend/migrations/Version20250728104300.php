<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to migrate existing nationality data to country references and clean up
 */
final class Version20250728104300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migrate existing nationality enum values to country references and remove nationality column';
    }

    public function up(Schema $schema): void
    {
        // Step 1: Update client table to set country_id based on nationality
        // This SQL will match nationality enum values to corresponding countries
        $this->addSql("
            UPDATE client 
            SET country_id = (
                SELECT c.id 
                FROM country c 
                WHERE LOWER(c.nationality) = LOWER(client.nationality)
            )
            WHERE nationality IS NOT NULL
        ");
        
        // Step 2: Drop the foreign key constraint temporarily
        $this->addSql('ALTER TABLE client DROP CONSTRAINT FK_C7440455F92F3E70');
        
        // Step 3: Drop the index temporarily
        $this->addSql('DROP INDEX IDX_C7440455F92F3E70 ON client');
        
        // Step 4: Make country_id NOT NULL after data migration
        $this->addSql('ALTER TABLE client ALTER COLUMN country_id UNIQUEIDENTIFIER NOT NULL');
        
        // Step 5: Recreate index and foreign key constraint
        $this->addSql('CREATE INDEX IDX_C7440455F92F3E70 ON client (country_id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        
        // Step 6: Remove the old nationality column
        $this->addSql('ALTER TABLE client DROP COLUMN nationality');
    }

    public function down(Schema $schema): void
    {
        // Add back nationality column
        $this->addSql('ALTER TABLE client ADD nationality NVARCHAR(255)');
        
        // Make country_id nullable again
        $this->addSql('ALTER TABLE client ALTER COLUMN country_id UNIQUEIDENTIFIER NULL');
        
        // Migrate data back from country to nationality
        $this->addSql("
            UPDATE client 
            SET nationality = (
                SELECT LOWER(c.nationality) 
                FROM country c 
                WHERE c.id = client.country_id
            )
            WHERE country_id IS NOT NULL
        ");
    }
}
