<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250610165102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cursus DROP FOREIGN KEY fk_cursus_theme
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lecon DROP FOREIGN KEY fk_lecon_cursus
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cursus
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lecon
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE cursus (id INT AUTO_INCREMENT NOT NULL, theme_id INT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prix DOUBLE PRECISION DEFAULT '0' NOT NULL, INDEX fk_cursus_theme (theme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lecon (id INT AUTO_INCREMENT NOT NULL, cursus_id INT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prix DOUBLE PRECISION DEFAULT '0' NOT NULL, INDEX fk_lecon_cursus (cursus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cursus ADD CONSTRAINT fk_cursus_theme FOREIGN KEY (theme_id) REFERENCES theme (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lecon ADD CONSTRAINT fk_lecon_cursus FOREIGN KEY (cursus_id) REFERENCES cursus (id) ON DELETE CASCADE
        SQL);
    }
}
