<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220525155759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE client_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE worker_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE client (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, client_id INT DEFAULT NULL, worker_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, number_phone VARCHAR(12) NOT NULL, birthdate DATE NOT NULL, surname VARCHAR(100) NOT NULL, name VARCHAR(100) NOT NULL, patronymic VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64919EB6921 ON "user" (client_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6496B20BA36 ON "user" (worker_id)');
        $this->addSql('CREATE TABLE worker (id INT NOT NULL, post_id INT NOT NULL, photo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9FB2BF624B89032C ON worker (post_id)');
        $this->addSql('CREATE TABLE worker_service (worker_id INT NOT NULL, service_id INT NOT NULL, PRIMARY KEY(worker_id, service_id))');
        $this->addSql('CREATE INDEX IDX_D572E19F6B20BA36 ON worker_service (worker_id)');
        $this->addSql('CREATE INDEX IDX_D572E19FED5CA9E6 ON worker_service (service_id)');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D64919EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D6496B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE worker ADD CONSTRAINT FK_9FB2BF624B89032C FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE worker_service ADD CONSTRAINT FK_D572E19F6B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE worker_service ADD CONSTRAINT FK_D572E19FED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD client_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD worker_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495519EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849556B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_42C8495519EB6921 ON reservation (client_id)');
        $this->addSql('CREATE INDEX IDX_42C849556B20BA36 ON reservation (worker_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C8495519EB6921');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D64919EB6921');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C849556B20BA36');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D6496B20BA36');
        $this->addSql('ALTER TABLE worker_service DROP CONSTRAINT FK_D572E19F6B20BA36');
        $this->addSql('DROP SEQUENCE client_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE worker_id_seq CASCADE');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE worker');
        $this->addSql('DROP TABLE worker_service');
        $this->addSql('DROP INDEX IDX_42C8495519EB6921');
        $this->addSql('DROP INDEX IDX_42C849556B20BA36');
        $this->addSql('ALTER TABLE reservation DROP client_id');
        $this->addSql('ALTER TABLE reservation DROP worker_id');
    }
}
