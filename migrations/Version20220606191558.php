<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220606191558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE kpi_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE kpi (id INT NOT NULL, worker_id INT NOT NULL, weight_volume_sales DOUBLE PRECISION NOT NULL, min_volume_sales DOUBLE PRECISION NOT NULL, fact_volume_sales DOUBLE PRECISION NOT NULL, planned_volume_sales DOUBLE PRECISION NOT NULL, weight_quality_service DOUBLE PRECISION NOT NULL, min_quality_service DOUBLE PRECISION NOT NULL, fact_quality_service DOUBLE PRECISION NOT NULL, planned_quality_service DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A0925DD96B20BA36 ON kpi (worker_id)');
        $this->addSql('ALTER TABLE kpi ADD CONSTRAINT FK_A0925DD96B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE kpi_id_seq CASCADE');
        $this->addSql('DROP TABLE kpi');
    }
}
