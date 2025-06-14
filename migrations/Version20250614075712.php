<?php

declare(strict_types=1);

namespace Psancho\Galeizon\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250614075712 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'table userprofile';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            create table userprofile (
                `id` int unsigned not null auto_increment,
                `username` varchar(32) not null,
                `firstname` varchar(32) default null,
                `lastname` varchar(32) default null,
                `email` varchar(64) default null,
                `last_access` timestamp null default null,
                `flags` tinyint unsigned not null default '0' comment '1: active',
                primary key (`id`),
                unique key `uuid` (`username`) using btree
            )
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<SQL
            drop table if exists userprofile
        SQL);
    }
}
