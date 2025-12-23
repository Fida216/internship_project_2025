<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250709084044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id UNIQUEIDENTIFIER NOT NULL, email NVARCHAR(255) NOT NULL, password_hash NVARCHAR(255) NOT NULL, created_at DATE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D3656A4E7927C74 ON account (email) WHERE email IS NOT NULL');
        $this->addSql('CREATE TABLE exchange_office (id UNIQUEIDENTIFIER NOT NULL, name NVARCHAR(255) NOT NULL, address NVARCHAR(255) NOT NULL, email NVARCHAR(255) NOT NULL, phone NVARCHAR(255) NOT NULL, owner NVARCHAR(255) NOT NULL, office_status NVARCHAR(255) NOT NULL, created_at DATE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE [user] (id UNIQUEIDENTIFIER NOT NULL, last_name NVARCHAR(255) NOT NULL, first_name NVARCHAR(255) NOT NULL, phone NVARCHAR(255) NOT NULL, role NVARCHAR(255) NOT NULL, status NVARCHAR(255) NOT NULL, created_at DATE NOT NULL, account_id UNIQUEIDENTIFIER, exchange_office_id UNIQUEIDENTIFIER, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6499B6B5FBA ON [user] (account_id) WHERE account_id IS NOT NULL');
        $this->addSql('CREATE INDEX IDX_8D93D649B2885B05 ON [user] (exchange_office_id)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT IDENTITY NOT NULL, body VARCHAR(MAX) NOT NULL, headers VARCHAR(MAX) NOT NULL, queue_name NVARCHAR(190) NOT NULL, created_at DATETIME2(6) NOT NULL, available_at DATETIME2(6) NOT NULL, delivered_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('ALTER TABLE [user] ADD CONSTRAINT FK_8D93D6499B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE [user] ADD CONSTRAINT FK_8D93D649B2885B05 FOREIGN KEY (exchange_office_id) REFERENCES exchange_office (id)');
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
        $this->addSql('ALTER TABLE [user] DROP CONSTRAINT FK_8D93D6499B6B5FBA');
        $this->addSql('ALTER TABLE [user] DROP CONSTRAINT FK_8D93D649B2885B05');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE exchange_office');
        $this->addSql('DROP TABLE [user]');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
