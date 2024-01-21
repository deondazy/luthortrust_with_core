<?php

declare(strict_types=1);

namespace Denosys\App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240120170743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE accounts (id BIGINT AUTO_INCREMENT NOT NULL, user_id BIGINT DEFAULT NULL, created_by BIGINT DEFAULT NULL, number VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, balance VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_CAC89EACA76ED395 (user_id), INDEX IDX_CAC89EACDE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE countries (id BIGINT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, iso VARCHAR(2) DEFAULT NULL, iso3 VARCHAR(3) DEFAULT NULL, num_code INT DEFAULT NULL, phone_code INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sessions (id BIGINT AUTO_INCREMENT NOT NULL, user_id BIGINT DEFAULT NULL, ip_address VARCHAR(255) NOT NULL, user_agent VARCHAR(255) NOT NULL, payload VARCHAR(255) NOT NULL, last_activity INT NOT NULL, INDEX IDX_9A609D13A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id BIGINT AUTO_INCREMENT NOT NULL, country BIGINT DEFAULT NULL, created_by BIGINT DEFAULT NULL, reference_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', first_name VARCHAR(255) NOT NULL, middle_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, gender VARCHAR(255) NOT NULL, date_of_birth DATE NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, mobile_number VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, roles JSON NOT NULL, pin VARCHAR(4) NOT NULL, passport VARCHAR(255) DEFAULT NULL, require_cot TINYINT(1) NOT NULL, require_imf TINYINT(1) NOT NULL, require_tax TINYINT(1) NOT NULL, cot_code VARCHAR(255) DEFAULT NULL, imf_code VARCHAR(255) DEFAULT NULL, tax_code VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_1483A5E91645DEA9 (reference_id), UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), INDEX IDX_1483A5E95373C966 (country), INDEX IDX_1483A5E9DE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE accounts ADD CONSTRAINT FK_CAC89EACA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE accounts ADD CONSTRAINT FK_CAC89EACDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE sessions ADD CONSTRAINT FK_9A609D13A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E95373C966 FOREIGN KEY (country) REFERENCES countries (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE accounts DROP FOREIGN KEY FK_CAC89EACA76ED395');
        $this->addSql('ALTER TABLE accounts DROP FOREIGN KEY FK_CAC89EACDE12AB56');
        $this->addSql('ALTER TABLE sessions DROP FOREIGN KEY FK_9A609D13A76ED395');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E95373C966');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9DE12AB56');
        $this->addSql('DROP TABLE accounts');
        $this->addSql('DROP TABLE countries');
        $this->addSql('DROP TABLE sessions');
        $this->addSql('DROP TABLE users');
    }
}
