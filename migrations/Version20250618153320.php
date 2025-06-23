<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250618153320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_lecon_validations (user_id INT NOT NULL, lecon_id INT NOT NULL, INDEX IDX_7D70BC6DA76ED395 (user_id), INDEX IDX_7D70BC6DEC1308A5 (lecon_id), PRIMARY KEY(user_id, lecon_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lecon_validations ADD CONSTRAINT FK_7D70BC6DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lecon_validations ADD CONSTRAINT FK_7D70BC6DEC1308A5 FOREIGN KEY (lecon_id) REFERENCES lecon (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lecon DROP is_validated
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lecon_validations DROP FOREIGN KEY FK_7D70BC6DA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lecon_validations DROP FOREIGN KEY FK_7D70BC6DEC1308A5
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_lecon_validations
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lecon ADD is_validated TINYINT(1) NOT NULL
        SQL);
    }
}
