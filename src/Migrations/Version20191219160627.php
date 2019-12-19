<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191219160627 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE article CHANGE subtitle subtitle VARCHAR(255) DEFAULT NULL, CHANGE reported reported TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7D8F3EC46');
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7D9D86650F');
        $this->addSql('DROP INDEX UNIQ_49CA4E7D9D86650F ON likes');
        $this->addSql('DROP INDEX IDX_49CA4E7D8F3EC46 ON likes');
        $this->addSql('ALTER TABLE likes DROP article_id_id, DROP user_id_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE article CHANGE subtitle subtitle VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE reported reported TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE likes ADD article_id_id INT NOT NULL, ADD user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7D8F3EC46 FOREIGN KEY (article_id_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7D9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_49CA4E7D9D86650F ON likes (user_id_id)');
        $this->addSql('CREATE INDEX IDX_49CA4E7D8F3EC46 ON likes (article_id_id)');
    }
}
