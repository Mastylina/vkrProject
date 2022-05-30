<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220528090253 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE feedback ADD service_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE feedback ADD worker_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D2294458ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D22944586B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D2294458ED5CA9E6 ON feedback (service_id)');
        $this->addSql('CREATE INDEX IDX_D22944586B20BA36 ON feedback (worker_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE feedback DROP CONSTRAINT FK_D2294458ED5CA9E6');
        $this->addSql('ALTER TABLE feedback DROP CONSTRAINT FK_D22944586B20BA36');
        $this->addSql('DROP INDEX IDX_D2294458ED5CA9E6');
        $this->addSql('DROP INDEX IDX_D22944586B20BA36');
        $this->addSql('ALTER TABLE feedback DROP service_id');
        $this->addSql('ALTER TABLE feedback DROP worker_id');
    }
}
