<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250515093629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    /**
    * @SuppressWarnings("UnusedFormalParameter")
    */
    public function upp(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE energy_share (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, year INTEGER NOT NULL, heating_industry DOUBLE PRECISION NOT NULL, electricity DOUBLE PRECISION NOT NULL, transport DOUBLE PRECISION NOT NULL, total DOUBLE PRECISION NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE project
        SQL);
    }

    /**
    * @SuppressWarnings("UnusedFormalParameter")
    */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE project (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE "BINARY")
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE energy_share
        SQL);
    }
}
