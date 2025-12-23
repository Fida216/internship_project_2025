<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250730100736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE campaignTargetClient (id UNIQUEIDENTIFIER NOT NULL, added_at DATE NOT NULL, marketing_campaign_id UNIQUEIDENTIFIER NOT NULL, client_id UNIQUEIDENTIFIER NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_683D230F893E6789 ON campaignTargetClient (marketing_campaign_id)');
        $this->addSql('CREATE INDEX IDX_683D230F19EB6921 ON campaignTargetClient (client_id)');
        $this->addSql('CREATE TABLE clientSegmentHistory (id UNIQUEIDENTIFIER NOT NULL, segment NVARCHAR(255) NOT NULL, created_at DATETIME2(6) NOT NULL, client_id UNIQUEIDENTIFIER NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_787F46AF19EB6921 ON clientSegmentHistory (client_id)');
        $this->addSql('CREATE TABLE marketingAction (id UNIQUEIDENTIFIER NOT NULL, title NVARCHAR(255) NOT NULL, channel_type NVARCHAR(255) NOT NULL, content VARCHAR(MAX) NOT NULL, status NVARCHAR(255) NOT NULL, sent_at DATE NOT NULL, user_id UNIQUEIDENTIFIER NOT NULL, campaign_id UNIQUEIDENTIFIER NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_AF6A9B1CA76ED395 ON marketingAction (user_id)');
        $this->addSql('CREATE INDEX IDX_AF6A9B1CF639F774 ON marketingAction (campaign_id)');
        $this->addSql('CREATE TABLE marketingCampaign (id UNIQUEIDENTIFIER NOT NULL, title NVARCHAR(255) NOT NULL, description NVARCHAR(255) NOT NULL, status NVARCHAR(255) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, created_at DATE NOT NULL, exchange_office_id UNIQUEIDENTIFIER NOT NULL, user_id UNIQUEIDENTIFIER NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_39C6CA79B2885B05 ON marketingCampaign (exchange_office_id)');
        $this->addSql('CREATE INDEX IDX_39C6CA79A76ED395 ON marketingCampaign (user_id)');
        $this->addSql('CREATE TABLE recommendation (id UNIQUEIDENTIFIER NOT NULL, recommendation_type NVARCHAR(255) NOT NULL, description NVARCHAR(255) NOT NULL, generated_at DATE NOT NULL, status NVARCHAR(255) NOT NULL, client_id UNIQUEIDENTIFIER NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_433224D219EB6921 ON recommendation (client_id)');
        $this->addSql('ALTER TABLE campaignTargetClient ADD CONSTRAINT FK_683D230F893E6789 FOREIGN KEY (marketing_campaign_id) REFERENCES marketingCampaign (id)');
        $this->addSql('ALTER TABLE campaignTargetClient ADD CONSTRAINT FK_683D230F19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE clientSegmentHistory ADD CONSTRAINT FK_787F46AF19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE marketingAction ADD CONSTRAINT FK_AF6A9B1CA76ED395 FOREIGN KEY (user_id) REFERENCES userInfo (id)');
        $this->addSql('ALTER TABLE marketingAction ADD CONSTRAINT FK_AF6A9B1CF639F774 FOREIGN KEY (campaign_id) REFERENCES marketingCampaign (id)');
        $this->addSql('ALTER TABLE marketingCampaign ADD CONSTRAINT FK_39C6CA79B2885B05 FOREIGN KEY (exchange_office_id) REFERENCES exchangeOffice (id)');
        $this->addSql('ALTER TABLE marketingCampaign ADD CONSTRAINT FK_39C6CA79A76ED395 FOREIGN KEY (user_id) REFERENCES userInfo (id)');
        $this->addSql('ALTER TABLE recommendation ADD CONSTRAINT FK_433224D219EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
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
        $this->addSql('ALTER TABLE campaignTargetClient DROP CONSTRAINT FK_683D230F893E6789');
        $this->addSql('ALTER TABLE campaignTargetClient DROP CONSTRAINT FK_683D230F19EB6921');
        $this->addSql('ALTER TABLE clientSegmentHistory DROP CONSTRAINT FK_787F46AF19EB6921');
        $this->addSql('ALTER TABLE marketingAction DROP CONSTRAINT FK_AF6A9B1CA76ED395');
        $this->addSql('ALTER TABLE marketingAction DROP CONSTRAINT FK_AF6A9B1CF639F774');
        $this->addSql('ALTER TABLE marketingCampaign DROP CONSTRAINT FK_39C6CA79B2885B05');
        $this->addSql('ALTER TABLE marketingCampaign DROP CONSTRAINT FK_39C6CA79A76ED395');
        $this->addSql('ALTER TABLE recommendation DROP CONSTRAINT FK_433224D219EB6921');
        $this->addSql('DROP TABLE campaignTargetClient');
        $this->addSql('DROP TABLE clientSegmentHistory');
        $this->addSql('DROP TABLE marketingAction');
        $this->addSql('DROP TABLE marketingCampaign');
        $this->addSql('DROP TABLE recommendation');
    }
}
