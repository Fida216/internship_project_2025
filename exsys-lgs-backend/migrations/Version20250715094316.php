<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250715094316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename table users to userInfo';
    }

    public function up(Schema $schema): void
    {
        // Rename the users table to userInfo
        $this->addSql('EXEC sp_rename \'users\', \'userInfo\'');
    }

    public function down(Schema $schema): void
    {
        // Rename back from userInfo to users
        $this->addSql('EXEC sp_rename \'userInfo\', \'users\'');
    }
}
