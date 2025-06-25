<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250625000415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_certification DROP FOREIGN KEY FK_82B2C025A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_certification DROP FOREIGN KEY FK_82B2C025CB47068A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_certification
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE certification DROP user_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_certification (user_id INT NOT NULL, certification_id INT NOT NULL, INDEX IDX_82B2C025A76ED395 (user_id), INDEX IDX_82B2C025CB47068A (certification_id), PRIMARY KEY(user_id, certification_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_certification ADD CONSTRAINT FK_82B2C025A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_certification ADD CONSTRAINT FK_82B2C025CB47068A FOREIGN KEY (certification_id) REFERENCES certification (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE certification ADD user_id INT NOT NULL
        SQL);
    }
}
