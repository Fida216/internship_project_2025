<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250728090734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quick_message (id UNIQUEIDENTIFIER NOT NULL, title NVARCHAR(255) NOT NULL, channel_type NVARCHAR(255) NOT NULL, content VARCHAR(MAX) NOT NULL, status NVARCHAR(255) NOT NULL, sent_at DATETIME2(6), created_at DATETIME2(6) NOT NULL, user_id UNIQUEIDENTIFIER NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_BC5713BBA76ED395 ON quick_message (user_id)');
        $this->addSql('CREATE TABLE quick_message_target_client (id UNIQUEIDENTIFIER NOT NULL, added_at DATETIME2(6) NOT NULL, quick_message_id UNIQUEIDENTIFIER NOT NULL, client_id UNIQUEIDENTIFIER NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_123CA6A33BAA51B8 ON quick_message_target_client (quick_message_id)');
        $this->addSql('CREATE INDEX IDX_123CA6A319EB6921 ON quick_message_target_client (client_id)');
        $this->addSql('ALTER TABLE quick_message ADD CONSTRAINT FK_BC5713BBA76ED395 FOREIGN KEY (user_id) REFERENCES userInfo (id)');
        $this->addSql('ALTER TABLE quick_message_target_client ADD CONSTRAINT FK_123CA6A33BAA51B8 FOREIGN KEY (quick_message_id) REFERENCES quick_message (id)');
        $this->addSql('ALTER TABLE quick_message_target_client ADD CONSTRAINT FK_123CA6A319EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
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
        $this->addSql('ALTER TABLE quick_message DROP CONSTRAINT FK_BC5713BBA76ED395');
        $this->addSql('ALTER TABLE quick_message_target_client DROP CONSTRAINT FK_123CA6A33BAA51B8');
        $this->addSql('ALTER TABLE quick_message_target_client DROP CONSTRAINT FK_123CA6A319EB6921');
        $this->addSql('DROP TABLE quick_message');
        $this->addSql('DROP TABLE quick_message_target_client');
    }
}
