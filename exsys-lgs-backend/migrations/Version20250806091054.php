<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250806091054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE marketingActionTargetClient DROP CONSTRAINT FK_E0B6A2E24CAF441C');
        $this->addSql('ALTER TABLE marketingActionTargetClient DROP CONSTRAINT FK_E0B6A2E219EB6921');
        $this->addSql('DROP TABLE marketingActionTargetClient');
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
        $this->addSql('CREATE TABLE marketingActionTargetClient (id UNIQUEIDENTIFIER NOT NULL, added_at DATETIME2(6) NOT NULL, marketingAction_id UNIQUEIDENTIFIER NOT NULL, client_id UNIQUEIDENTIFIER NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_E0B6A2E24CAF441C ON marketingActionTargetClient (marketingAction_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_E0B6A2E219EB6921 ON marketingActionTargetClient (client_id)');
        $this->addSql('ALTER TABLE marketingActionTargetClient ADD CONSTRAINT FK_E0B6A2E24CAF441C FOREIGN KEY (marketingAction_id) REFERENCES marketingAction (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE marketingActionTargetClient ADD CONSTRAINT FK_E0B6A2E219EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
