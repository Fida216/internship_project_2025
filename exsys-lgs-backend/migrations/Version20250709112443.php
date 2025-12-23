<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250709112443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE users (id UNIQUEIDENTIFIER NOT NULL, last_name NVARCHAR(255) NOT NULL, first_name NVARCHAR(255) NOT NULL, phone NVARCHAR(255) NOT NULL, role NVARCHAR(255) NOT NULL, status NVARCHAR(255) NOT NULL, created_at DATE NOT NULL, account_id UNIQUEIDENTIFIER, exchange_office_id UNIQUEIDENTIFIER, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E99B6B5FBA ON users (account_id) WHERE account_id IS NOT NULL');
        $this->addSql('CREATE INDEX IDX_1483A5E9B2885B05 ON users (exchange_office_id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E99B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9B2885B05 FOREIGN KEY (exchange_office_id) REFERENCES exchange_office (id)');
        $this->addSql('ALTER TABLE [user] DROP CONSTRAINT FK_8D93D6499B6B5FBA');
        $this->addSql('ALTER TABLE [user] DROP CONSTRAINT FK_8D93D649B2885B05');
        $this->addSql('DROP TABLE [user]');
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
        $this->addSql('CREATE TABLE [user] (id UNIQUEIDENTIFIER NOT NULL, last_name NVARCHAR(255) COLLATE French_CI_AS NOT NULL, first_name NVARCHAR(255) COLLATE French_CI_AS NOT NULL, phone NVARCHAR(255) COLLATE French_CI_AS NOT NULL, role NVARCHAR(255) COLLATE French_CI_AS NOT NULL, status NVARCHAR(255) COLLATE French_CI_AS NOT NULL, created_at DATE NOT NULL, account_id UNIQUEIDENTIFIER, exchange_office_id UNIQUEIDENTIFIER, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE NONCLUSTERED INDEX UNIQ_8D93D6499B6B5FBA ON [user] (account_id) WHERE account_id IS NOT NULL');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_8D93D649B2885B05 ON [user] (exchange_office_id)');
        $this->addSql('ALTER TABLE [user] ADD CONSTRAINT FK_8D93D6499B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE [user] ADD CONSTRAINT FK_8D93D649B2885B05 FOREIGN KEY (exchange_office_id) REFERENCES exchange_office (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E99B6B5FBA');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E9B2885B05');
        $this->addSql('DROP TABLE users');
    }
}
