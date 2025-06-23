<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250616214638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_purchase ADD cursus_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_purchase ADD CONSTRAINT FK_819A353B40AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_819A353B40AEF4B9 ON user_purchase (cursus_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_purchase DROP FOREIGN KEY FK_819A353B40AEF4B9
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_819A353B40AEF4B9 ON user_purchase
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_purchase DROP cursus_id
        SQL);
    }
}
