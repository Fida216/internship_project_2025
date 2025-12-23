<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250728111433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove iso3_code and numeric_code columns from country table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE country DROP COLUMN iso3_code');
        $this->addSql('ALTER TABLE country DROP COLUMN numeric_code');
    }

    public function down(Schema $schema): void
    {
        // Add back the columns if needed
        $this->addSql('ALTER TABLE country ADD iso3_code NVARCHAR(3)');
        $this->addSql('ALTER TABLE country ADD numeric_code NVARCHAR(3)');
    }
}
