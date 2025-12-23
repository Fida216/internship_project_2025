<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250729104151 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quickMessage ADD exchange_office_id UNIQUEIDENTIFIER NOT NULL');
        $this->addSql('ALTER TABLE quickMessage ADD CONSTRAINT FK_6ABE0DEEB2885B05 FOREIGN KEY (exchange_office_id) REFERENCES exchangeOffice (id)');
        $this->addSql('CREATE INDEX IDX_6ABE0DEEB2885B05 ON quickMessage (exchange_office_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA db_accessadmin');
        $this->addSql('CREATE SCHEMA db_backupoperator');
        $this->addSql('CREATE SCHEMA db_datareader');
        $this->addSql('CREATE SCHEMA db_datawriter');
        $this->addSql('CREATE SCHEMA db_ddladmin');
        $this->addSql('CREATE SCHEMA db_denydatareader');
        $this->addSql('CREATE SCHEMA db_denydatawriter');
        $this->addSql('CREATE SCHEMA db_owner');
        $this->addSql('CREATE SCHEMA db_securityadmin');
        $this->addSql('CREATE SCHEMA dbo');
        $this->addSql('ALTER TABLE quickMessage DROP CONSTRAINT FK_6ABE0DEEB2885B05');
        $this->addSql('DROP INDEX IDX_6ABE0DEEB2885B05 ON quickMessage');
        $this->addSql('ALTER TABLE quickMessage DROP COLUMN exchange_office_id');
    }
}
