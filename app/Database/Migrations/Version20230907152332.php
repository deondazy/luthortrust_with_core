<?php

declare(strict_types=1);

namespace Denosys\App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230907152332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users CHANGE cot_code cot_code VARCHAR(255) DEFAULT NULL, CHANGE imf_code imf_code VARCHAR(255) DEFAULT NULL, CHANGE tax_code tax_code VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users CHANGE cot_code cot_code VARCHAR(255) NOT NULL, CHANGE imf_code imf_code VARCHAR(255) NOT NULL, CHANGE tax_code tax_code VARCHAR(255) NOT NULL');
    }
}
