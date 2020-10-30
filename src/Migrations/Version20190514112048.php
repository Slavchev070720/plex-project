<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190514112048 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return '';
    }

    /**
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE IF NOT EXISTS episode (id INT AUTO_INCREMENT NOT NULL, series_id INT NOT NULL, title VARCHAR(255) NOT NULL, season INT NOT NULL, episode INT NOT NULL, imdb_id VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, subtitle_lang VARCHAR(255) DEFAULT NULL, audio_lang VARCHAR(255) DEFAULT NULL, writer VARCHAR(255) DEFAULT NULL, director VARCHAR(255) DEFAULT NULL, release_date DATE DEFAULT NULL, poster_url VARCHAR(255) DEFAULT NULL, rating DOUBLE PRECISION DEFAULT NULL, duration INT DEFAULT NULL, status INT NOT NULL, error_audio TINYINT(1) NOT NULL, error_subtitle TINYINT(1) NOT NULL, INDEX IDX_DDAA1CDA5278319C (series_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS series (id INT AUTO_INCREMENT NOT NULL, imdb_id VARCHAR(255) DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, release_date DATE DEFAULT NULL, poster_url VARCHAR(255) DEFAULT NULL, rating DOUBLE PRECISION DEFAULT NULL, duration INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS movie (id INT AUTO_INCREMENT NOT NULL, imdb_id VARCHAR(255) DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, release_date DATE DEFAULT NULL, subtitle_lang VARCHAR(255) DEFAULT NULL, audio_lang VARCHAR(255) DEFAULT NULL, writer VARCHAR(255) DEFAULT NULL, director VARCHAR(255) DEFAULT NULL, poster_url VARCHAR(255) DEFAULT NULL, rating DOUBLE PRECISION DEFAULT NULL, status INT NOT NULL, error_audio TINYINT(1) NOT NULL, error_subtitle TINYINT(1) NOT NULL, duration INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE IF EXISTS sample_table');
    }

    /**
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE IF NOT EXISTS sample_table (sample_table_id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(sample_table_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE IF EXISTS episode');
        $this->addSql('DROP TABLE IF EXISTS series');
        $this->addSql('DROP TABLE IF EXISTS movie');
    }
}
