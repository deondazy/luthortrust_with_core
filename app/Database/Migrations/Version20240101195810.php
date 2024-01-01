<?php

declare(strict_types=1);

namespace Denosys\App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240101195810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE countries_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sessions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE countries (id BIGINT NOT NULL, name VARCHAR(255) NOT NULL, iso VARCHAR(2) DEFAULT NULL, iso3 VARCHAR(3) DEFAULT NULL, num_code INT DEFAULT NULL, phone_code INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE sessions (id BIGINT NOT NULL, user_id BIGINT DEFAULT NULL, ip_address VARCHAR(255) NOT NULL, user_agent VARCHAR(255) NOT NULL, payload VARCHAR(255) NOT NULL, last_activity INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9A609D13A76ED395 ON sessions (user_id)');
        $this->addSql('CREATE TABLE users (id BIGINT NOT NULL, country BIGINT DEFAULT NULL, created_by BIGINT DEFAULT NULL, reference_id UUID NOT NULL, first_name VARCHAR(255) NOT NULL, middle_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, gender VARCHAR(255) NOT NULL, date_of_birth DATE NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, mobile_number VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, is_active BOOLEAN NOT NULL, roles JSON NOT NULL, pin VARCHAR(4) NOT NULL, passport VARCHAR(255) DEFAULT NULL, require_cot BOOLEAN NOT NULL, require_imf BOOLEAN NOT NULL, require_tax BOOLEAN NOT NULL, cot_code VARCHAR(255) DEFAULT NULL, imf_code VARCHAR(255) DEFAULT NULL, tax_code VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E91645DEA9 ON users (reference_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('CREATE INDEX IDX_1483A5E95373C966 ON users (country)');
        $this->addSql('CREATE INDEX IDX_1483A5E9DE12AB56 ON users (created_by)');
        $this->addSql('ALTER TABLE sessions ADD CONSTRAINT FK_9A609D13A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E95373C966 FOREIGN KEY (country) REFERENCES countries (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE countries_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sessions_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('ALTER TABLE sessions DROP CONSTRAINT FK_9A609D13A76ED395');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E95373C966');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E9DE12AB56');
        $this->addSql('DROP TABLE countries');
        $this->addSql('DROP TABLE sessions');
        $this->addSql('DROP TABLE users');
    }
}
