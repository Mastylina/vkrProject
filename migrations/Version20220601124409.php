<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220601124409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE feedback_worker ADD client_id INT NOT NULL');
        $this->addSql('ALTER TABLE feedback_worker ADD worker_id INT NOT NULL');
        $this->addSql('ALTER TABLE feedback_worker ADD text TEXT NOT NULL');
        $this->addSql('ALTER TABLE feedback_worker ADD date_and_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE feedback_worker ADD estimation INT NOT NULL');
        $this->addSql('ALTER TABLE feedback_worker ADD CONSTRAINT FK_A2FE8B2C19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_worker ADD CONSTRAINT FK_A2FE8B2C6B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A2FE8B2C19EB6921 ON feedback_worker (client_id)');
        $this->addSql('CREATE INDEX IDX_A2FE8B2C6B20BA36 ON feedback_worker (worker_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE feedback_worker DROP CONSTRAINT FK_A2FE8B2C19EB6921');
        $this->addSql('ALTER TABLE feedback_worker DROP CONSTRAINT FK_A2FE8B2C6B20BA36');
        $this->addSql('DROP INDEX IDX_A2FE8B2C19EB6921');
        $this->addSql('DROP INDEX IDX_A2FE8B2C6B20BA36');
        $this->addSql('ALTER TABLE feedback_worker DROP client_id');
        $this->addSql('ALTER TABLE feedback_worker DROP worker_id');
        $this->addSql('ALTER TABLE feedback_worker DROP text');
        $this->addSql('ALTER TABLE feedback_worker DROP date_and_time');
        $this->addSql('ALTER TABLE feedback_worker DROP estimation');
    }
}
