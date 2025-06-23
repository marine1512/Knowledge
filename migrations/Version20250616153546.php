<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250616153546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_lecons (user_id INT NOT NULL, lecon_id INT NOT NULL, INDEX IDX_A21E3FDDA76ED395 (user_id), INDEX IDX_A21E3FDDEC1308A5 (lecon_id), PRIMARY KEY(user_id, lecon_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_cursus (user_id INT NOT NULL, cursus_id INT NOT NULL, INDEX IDX_6707BBFEA76ED395 (user_id), INDEX IDX_6707BBFE40AEF4B9 (cursus_id), PRIMARY KEY(user_id, cursus_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lecons ADD CONSTRAINT FK_A21E3FDDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lecons ADD CONSTRAINT FK_A21E3FDDEC1308A5 FOREIGN KEY (lecon_id) REFERENCES lecon (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_cursus ADD CONSTRAINT FK_6707BBFEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_cursus ADD CONSTRAINT FK_6707BBFE40AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lecons DROP FOREIGN KEY FK_A21E3FDDA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lecons DROP FOREIGN KEY FK_A21E3FDDEC1308A5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_cursus DROP FOREIGN KEY FK_6707BBFEA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_cursus DROP FOREIGN KEY FK_6707BBFE40AEF4B9
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_lecons
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_cursus
        SQL);
    }
}
