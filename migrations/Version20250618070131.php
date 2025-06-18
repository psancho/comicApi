<?php

declare(strict_types=1);

namespace Psancho\Galeizon\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250618070131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Nouvelle colonne userprofile.profile';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            alter table userprofile
            add column `profile` tinyint unsigned not null default 1 after last_access;
        SQL);

    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<SQL
            alter table userprofile
            drop column `profile`
        SQL);
    }
}
