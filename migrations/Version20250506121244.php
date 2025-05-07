<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250506121244 extends AbstractMigration
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
            CREATE TABLE library (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titel VARCHAR(255) NOT NULL, isbn INTEGER NOT NULL, author VARCHAR(255) NOT NULL, image_url VARCHAR(255) DEFAULT NULL)
        SQL);
    }

    /**
    * @SuppressWarnings("UnusedFormalParameter")
    */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE library
        SQL);
    }
}
