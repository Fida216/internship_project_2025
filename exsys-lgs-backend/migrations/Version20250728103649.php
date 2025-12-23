<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250728103649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create country table to replace nationality enum';
    }

    public function up(Schema $schema): void
    {
        // Create country table
        $this->addSql('CREATE TABLE country (
            id UNIQUEIDENTIFIER NOT NULL, 
            code NVARCHAR(100) NOT NULL, 
            name NVARCHAR(255) NOT NULL, 
            nationality NVARCHAR(255) NOT NULL, 
            iso3_code NVARCHAR(3), 
            numeric_code NVARCHAR(3), 
            is_active BIT NOT NULL, 
            PRIMARY KEY (id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5373C96677153098 ON country (code)');
    }

    public function down(Schema $schema): void
    {
        // Drop country table
        $this->addSql('DROP TABLE country');
    }
}
