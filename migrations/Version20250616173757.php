<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250616173757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_purchase (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, lecon_id INT DEFAULT NULL, INDEX IDX_819A353BA76ED395 (user_id), INDEX IDX_819A353BEC1308A5 (lecon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_purchase ADD CONSTRAINT FK_819A353BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_purchase ADD CONSTRAINT FK_819A353BEC1308A5 FOREIGN KEY (lecon_id) REFERENCES lecon (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_purchase DROP FOREIGN KEY FK_819A353BA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_purchase DROP FOREIGN KEY FK_819A353BEC1308A5
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_purchase
        SQL);
    }
}
