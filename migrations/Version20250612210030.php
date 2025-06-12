<?php

declare(strict_types=1);

namespace Psancho\Galeizon\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250612210030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'table l10n';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            create table l10n (
            `key` varchar(256) not null,
            `locale` varchar(8) not null,
            `label` text,
            `scope` set('webapp') default null,
            primary key (`key`,`locale`)
            )
        SQL);

    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<SQL
            drop table if exists l10n
        SQL);
    }
}
