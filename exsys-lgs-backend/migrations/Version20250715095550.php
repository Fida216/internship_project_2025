<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250715095550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename tables: account→userCredential, exchange_office→exchangeOffice, clients→client';
    }

    public function up(Schema $schema): void
    {
        // Rename tables to match entity names
        $this->addSql('EXEC sp_rename \'account\', \'userCredential\'');
        $this->addSql('EXEC sp_rename \'exchange_office\', \'exchangeOffice\'');
        // Note: clients->client already renamed in previous migration
        
        // Update foreign key constraints for userInfo table
        $this->addSql('ALTER TABLE userInfo DROP CONSTRAINT FK_1483A5E99B6B5FBA');
        $this->addSql('ALTER TABLE userInfo DROP CONSTRAINT FK_1483A5E9B2885B05');
        $this->addSql('ALTER TABLE userInfo ADD CONSTRAINT FK_CDC6E6189B6B5FBA FOREIGN KEY (account_id) REFERENCES userCredential (id)');
        $this->addSql('ALTER TABLE userInfo ADD CONSTRAINT FK_CDC6E618B2885B05 FOREIGN KEY (exchange_office_id) REFERENCES exchangeOffice (id)');
        
        // Update client foreign key constraint
        $this->addSql('ALTER TABLE client DROP CONSTRAINT FK_C82E74B2885B05');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455B2885B05 FOREIGN KEY (exchange_office_id) REFERENCES exchangeOffice (id)');
    }

    public function down(Schema $schema): void
    {
        // Rename tables back to original names
        $this->addSql('EXEC sp_rename \'userCredential\', \'account\'');
        $this->addSql('EXEC sp_rename \'exchangeOffice\', \'exchange_office\'');
        // Note: client->clients will be reverted by the previous migration rollback
        
        // Revert foreign key constraints for userInfo table
        $this->addSql('ALTER TABLE userInfo DROP CONSTRAINT FK_CDC6E6189B6B5FBA');
        $this->addSql('ALTER TABLE userInfo DROP CONSTRAINT FK_CDC6E618B2885B05');
        $this->addSql('ALTER TABLE userInfo ADD CONSTRAINT FK_1483A5E99B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE userInfo ADD CONSTRAINT FK_1483A5E9B2885B05 FOREIGN KEY (exchange_office_id) REFERENCES exchange_office (id)');
        
        // Revert client foreign key constraint
        $this->addSql('ALTER TABLE client DROP CONSTRAINT FK_C7440455B2885B05');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C82E74B2885B05 FOREIGN KEY (exchange_office_id) REFERENCES exchange_office (id)');
    }
}
