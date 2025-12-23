<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250711094110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clients (id UNIQUEIDENTIFIER NOT NULL, last_name NVARCHAR(255) NOT NULL, first_name NVARCHAR(255) NOT NULL, birth_date DATE NOT NULL, email NVARCHAR(255) NOT NULL, phone NVARCHAR(255) NOT NULL, whatsapp NVARCHAR(255), national_id NVARCHAR(255), passport NVARCHAR(255), nationality NVARCHAR(255) NOT NULL, residence NVARCHAR(255) NOT NULL, gender NVARCHAR(255) NOT NULL, acquisition_source NVARCHAR(255) NOT NULL, status NVARCHAR(255) NOT NULL, current_segment NVARCHAR(255), created_at DATE NOT NULL, exchange_office_id UNIQUEIDENTIFIER, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_C82E74B2885B05 ON clients (exchange_office_id)');
        $this->addSql('ALTER TABLE clients ADD CONSTRAINT FK_C82E74B2885B05 FOREIGN KEY (exchange_office_id) REFERENCES exchange_office (id)');
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
        $this->addSql('ALTER TABLE clients DROP CONSTRAINT FK_C82E74B2885B05');
        $this->addSql('DROP TABLE clients');
    }
}
