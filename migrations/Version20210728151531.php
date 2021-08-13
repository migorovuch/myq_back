<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210728151531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE booking (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', schedule_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', client_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', status SMALLINT NOT NULL, start DATETIME NOT NULL, end DATETIME NOT NULL, title VARCHAR(255) DEFAULT NULL, customer_comment VARCHAR(500) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E00CEDDEA40BC2D5 (schedule_id), INDEX IDX_E00CEDDE19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, address_link VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, photos VARCHAR(255) DEFAULT NULL, status SMALLINT NOT NULL, name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_4FBF094FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_client (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', company_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, status SMALLINT NOT NULL, pseudonym VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_FADB6020A76ED395 (user_id), INDEX IDX_FADB6020979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedule (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', company_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, available TINYINT(1) NOT NULL, booking_duration INT NOT NULL, min_booking_time INT NOT NULL, max_booking_time INT NOT NULL, description LONGTEXT DEFAULT NULL, booking_condition SMALLINT DEFAULT NULL, accept_booking_condition SMALLINT DEFAULT NULL, accept_booking_time INT DEFAULT NULL, time_between_bookings INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_5A3811FB979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE special_hours (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', schedule_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ranges JSON NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, repeat_condition SMALLINT NOT NULL, repeat_day SMALLINT DEFAULT NULL, repeat_date DATETIME DEFAULT NULL, available TINYINT(1) DEFAULT NULL, INDEX IDX_262413CCA40BC2D5 (schedule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', email VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, nickname VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, status INT NOT NULL, phone VARCHAR(255) DEFAULT NULL, date_create DATETIME NOT NULL, date_update DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649A188FE64 (nickname), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEA40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE19EB6921 FOREIGN KEY (client_id) REFERENCES company_client (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE company_client ADD CONSTRAINT FK_FADB6020A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE company_client ADD CONSTRAINT FK_FADB6020979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE special_hours ADD CONSTRAINT FK_262413CCA40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company_client DROP FOREIGN KEY FK_FADB6020979B1AD6');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB979B1AD6');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE19EB6921');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEA40BC2D5');
        $this->addSql('ALTER TABLE special_hours DROP FOREIGN KEY FK_262413CCA40BC2D5');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FA76ED395');
        $this->addSql('ALTER TABLE company_client DROP FOREIGN KEY FK_FADB6020A76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE company_client');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('DROP TABLE special_hours');
        $this->addSql('DROP TABLE user');
    }
}
