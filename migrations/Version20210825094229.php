<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210825094229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company_chat (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', company_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', chat_id VARCHAR(255) NOT NULL, chat_language VARCHAR(255) NOT NULL, payload VARCHAR(255) DEFAULT NULL, INDEX IDX_26F0E844979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company_chat ADD CONSTRAINT FK_26F0E844979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company ADD access_token VARCHAR(255) DEFAULT NULL');
        $this->addSql("UPDATE company SET access_token=CONCAT('cmp:',MD5(CONCAT(id, created_at))) WHERE access_token IS NULL");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE company_chat');
        $this->addSql('ALTER TABLE company DROP access_token');
    }
}
