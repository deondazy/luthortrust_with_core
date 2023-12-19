<?php

declare(strict_types=1);

namespace Denosys\App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230921191521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E93174800F');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9F92F3E70');
        $this->addSql('DROP INDEX IDX_1483A5E9F92F3E70 ON users');
        $this->addSql('DROP INDEX IDX_1483A5E93174800F ON users');
        $this->addSql('ALTER TABLE users ADD country BIGINT DEFAULT NULL, ADD created_by BIGINT DEFAULT NULL, DROP country_id, DROP createdBy_id');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E95373C966 FOREIGN KEY (country) REFERENCES countries (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E95373C966 ON users (country)');
        $this->addSql('CREATE INDEX IDX_1483A5E9DE12AB56 ON users (created_by)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E95373C966');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9DE12AB56');
        $this->addSql('DROP INDEX IDX_1483A5E95373C966 ON users');
        $this->addSql('DROP INDEX IDX_1483A5E9DE12AB56 ON users');
        $this->addSql('ALTER TABLE users ADD country_id BIGINT DEFAULT NULL, ADD createdBy_id BIGINT DEFAULT NULL, DROP country, DROP created_by');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E93174800F FOREIGN KEY (createdBy_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9F92F3E70 FOREIGN KEY (country_id) REFERENCES countries (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E9F92F3E70 ON users (country_id)');
        $this->addSql('CREATE INDEX IDX_1483A5E93174800F ON users (createdBy_id)');
    }
}
