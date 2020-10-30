<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190617082828 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    /**
     * @param Schema $schema
     * @throws DBALException
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE INDEX series_title_idx ON series (title)');
        $this->addSql('ALTER TABLE movie CHANGE imdb_id imdb_id VARCHAR(9) DEFAULT NULL');
        $this->addSql('CREATE INDEX movie_title_idx ON movie (title)');
    }

    /**
     * @param Schema $schema
     * @throws DBALException
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX movie_title_idx ON movie');
        $this->addSql('ALTER TABLE movie CHANGE imdb_id imdb_id VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('DROP INDEX series_title_idx ON series');
    }
}
