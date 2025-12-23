<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250728112034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove is_active column from country table';
    }

    public function up(Schema $schema): void
    {
        // Remove is_active column from country table
        $this->addSql('ALTER TABLE country DROP COLUMN is_active');
    }

    public function down(Schema $schema): void
    {
        // Add back is_active column
        $this->addSql('ALTER TABLE country ADD is_active BIT NOT NULL DEFAULT 1');
    }
}
