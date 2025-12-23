<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250728101701 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename table quick_message to quickMessage and quick_message_target_client to quickMessageTargetClient';
    }

    public function up(Schema $schema): void
    {
        // Rename quick_message table to quickMessage (SQL Server syntax)
        $this->addSql("EXEC sp_rename 'quick_message', 'quickMessage'");
        
        // Rename quick_message_target_client table to quickMessageTargetClient (SQL Server syntax)
        $this->addSql("EXEC sp_rename 'quick_message_target_client', 'quickMessageTargetClient'");
        
        // Rename the foreign key column from quick_message_id to quickMessage_id (SQL Server syntax)
        $this->addSql("EXEC sp_rename 'quickMessageTargetClient.quick_message_id', 'quickMessage_id', 'COLUMN'");
    }

    public function down(Schema $schema): void
    {
        // Reverse the column rename (SQL Server syntax)
        $this->addSql("EXEC sp_rename 'quickMessageTargetClient.quickMessage_id', 'quick_message_id', 'COLUMN'");
        
        // Reverse the table renames (SQL Server syntax)
        $this->addSql("EXEC sp_rename 'quickMessage', 'quick_message'");
        $this->addSql("EXEC sp_rename 'quickMessageTargetClient', 'quick_message_target_client'");
    }
}
