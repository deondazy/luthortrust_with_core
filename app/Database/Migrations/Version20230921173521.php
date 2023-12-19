<?php

declare(strict_types=1);

namespace Denosys\App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230921173521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD createdBy_id BIGINT DEFAULT NULL, DROP created_by');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E93174800F FOREIGN KEY (createdBy_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E93174800F ON users (createdBy_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E93174800F');
        $this->addSql('DROP INDEX IDX_1483A5E93174800F ON users');
        $this->addSql('ALTER TABLE users ADD created_by BIGINT NOT NULL, DROP createdBy_id');
    }
}
