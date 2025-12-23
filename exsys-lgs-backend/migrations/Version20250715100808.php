<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250715100808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix index names after table renaming';
    }

    public function up(Schema $schema): void
    {
        // Update index names to match new table structure
        $this->addSql('EXEC sp_rename N\'client.idx_c82e74b2885b05\', N\'IDX_C7440455B2885B05\', N\'INDEX\'');
        $this->addSql('EXEC sp_rename N\'userCredential.uniq_7d3656a4e7927c74\', N\'UNIQ_75E3262E7927C74\', N\'INDEX\'');
    }

    public function down(Schema $schema): void
    {
        // Revert index names
        $this->addSql('EXEC sp_rename N\'client.IDX_C7440455B2885B05\', N\'idx_c82e74b2885b05\', N\'INDEX\'');
        $this->addSql('EXEC sp_rename N\'userCredential.UNIQ_75E3262E7927C74\', N\'uniq_7d3656a4e7927c74\', N\'INDEX\'');
    }
}
