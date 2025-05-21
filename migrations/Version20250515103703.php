<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250515103703 extends AbstractMigration
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
            CREATE TABLE energy_intensity (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, year INTEGER NOT NULL, percent_change DOUBLE PRECISION NOT NULL)
        SQL);
    }

    /**
    * @SuppressWarnings("UnusedFormalParameter")
    */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE energy_intensity
        SQL);
    }
}
