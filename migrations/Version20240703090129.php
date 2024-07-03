<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240703090129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tour DROP CONSTRAINT fk_6ad1f969ceee0a5');
        $this->addSql('DROP INDEX idx_6ad1f969ceee0a5');
        $this->addSql('ALTER TABLE tour RENAME COLUMN tornament_id TO tournament_id');
        $this->addSql('ALTER TABLE tour ADD CONSTRAINT FK_6AD1F96933D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6AD1F96933D1A3E7 ON tour (tournament_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tour DROP CONSTRAINT FK_6AD1F96933D1A3E7');
        $this->addSql('DROP INDEX IDX_6AD1F96933D1A3E7');
        $this->addSql('ALTER TABLE tour RENAME COLUMN tournament_id TO tornament_id');
        $this->addSql('ALTER TABLE tour ADD CONSTRAINT fk_6ad1f969ceee0a5 FOREIGN KEY (tornament_id) REFERENCES tournament (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_6ad1f969ceee0a5 ON tour (tornament_id)');
    }
}
