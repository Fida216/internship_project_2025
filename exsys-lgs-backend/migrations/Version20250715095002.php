<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250715095002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename table clients to client';
    }

    public function up(Schema $schema): void
    {
        // Rename the clients table to client
        $this->addSql('EXEC sp_rename \'clients\', \'client\'');
        
        // Update index names for userInfo table (from previous migration)
        $this->addSql('EXEC sp_rename N\'userInfo.uniq_1483a5e99b6b5fba\', N\'UNIQ_CDC6E6189B6B5FBA\', N\'INDEX\'');
        $this->addSql('EXEC sp_rename N\'userInfo.idx_1483a5e9b2885b05\', N\'IDX_CDC6E618B2885B05\', N\'INDEX\'');
    }

    public function down(Schema $schema): void
    {
        // Rename back from client to clients
        $this->addSql('EXEC sp_rename \'client\', \'clients\'');
        
        // Revert index names for userInfo table
        $this->addSql('EXEC sp_rename N\'userInfo.uniq_cdc6e6189b6b5fba\', N\'UNIQ_1483A5E99B6B5FBA\', N\'INDEX\'');
        $this->addSql('EXEC sp_rename N\'userInfo.idx_cdc6e618b2885b05\', N\'IDX_1483A5E9B2885B05\', N\'INDEX\'');
    }
}
