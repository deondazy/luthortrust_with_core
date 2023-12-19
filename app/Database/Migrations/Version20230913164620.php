<?php

declare(strict_types=1);

namespace Denosys\App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230913164620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE countries CHANGE iso iso VARCHAR(2) DEFAULT NULL, CHANGE iso3 iso3 VARCHAR(3) DEFAULT NULL, CHANGE num_code num_code INT DEFAULT NULL, CHANGE phone_code phone_code INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE countries CHANGE iso iso VARCHAR(255) DEFAULT NULL, CHANGE iso3 iso3 VARCHAR(255) DEFAULT NULL, CHANGE num_code num_code VARCHAR(255) DEFAULT NULL, CHANGE phone_code phone_code VARCHAR(255) DEFAULT NULL');
    }
}
