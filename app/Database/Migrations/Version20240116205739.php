<?php

declare(strict_types=1);

namespace Denosys\App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240116205739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE accounts_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE accounts (id BIGINT NOT NULL, user_id BIGINT DEFAULT NULL, created_by BIGINT DEFAULT NULL, number VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, balance VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CAC89EACA76ED395 ON accounts (user_id)');
        $this->addSql('CREATE INDEX IDX_CAC89EACDE12AB56 ON accounts (created_by)');
        $this->addSql('ALTER TABLE accounts ADD CONSTRAINT FK_CAC89EACA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE accounts ADD CONSTRAINT FK_CAC89EACDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE accounts_id_seq CASCADE');
        $this->addSql('ALTER TABLE accounts DROP CONSTRAINT FK_CAC89EACA76ED395');
        $this->addSql('ALTER TABLE accounts DROP CONSTRAINT FK_CAC89EACDE12AB56');
        $this->addSql('DROP TABLE accounts');
    }
}
