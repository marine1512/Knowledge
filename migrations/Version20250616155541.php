<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250616155541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_lecon (user_id INT NOT NULL, lecon_id INT NOT NULL, INDEX IDX_7624DF76A76ED395 (user_id), INDEX IDX_7624DF76EC1308A5 (lecon_id), PRIMARY KEY(user_id, lecon_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lecon ADD CONSTRAINT FK_7624DF76A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lecon ADD CONSTRAINT FK_7624DF76EC1308A5 FOREIGN KEY (lecon_id) REFERENCES lecon (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lecons DROP FOREIGN KEY FK_A21E3FDDA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lecons DROP FOREIGN KEY FK_A21E3FDDEC1308A5
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_lecons
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_lecons (user_id INT NOT NULL, lecon_id INT NOT NULL, INDEX IDX_A21E3FDDEC1308A5 (lecon_id), INDEX IDX_A21E3FDDA76ED395 (user_id), PRIMARY KEY(user_id, lecon_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lecons ADD CONSTRAINT FK_A21E3FDDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lecons ADD CONSTRAINT FK_A21E3FDDEC1308A5 FOREIGN KEY (lecon_id) REFERENCES lecon (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lecon DROP FOREIGN KEY FK_7624DF76A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lecon DROP FOREIGN KEY FK_7624DF76EC1308A5
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_lecon
        SQL);
    }
}
