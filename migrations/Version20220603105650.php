<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220603105650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message ADD worker_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD client_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD recipient_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD sender_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F6B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B6BD307F6B20BA36 ON message (worker_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F19EB6921 ON message (client_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F6B20BA36');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F19EB6921');
        $this->addSql('DROP INDEX IDX_B6BD307F6B20BA36');
        $this->addSql('DROP INDEX IDX_B6BD307F19EB6921');
        $this->addSql('ALTER TABLE message DROP worker_id');
        $this->addSql('ALTER TABLE message DROP client_id');
        $this->addSql('ALTER TABLE message DROP recipient_id');
        $this->addSql('ALTER TABLE message DROP sender_id');
    }
}
