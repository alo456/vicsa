<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181101162938 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER SEQUENCE activation_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE address_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE client_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE contract_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE device_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE device_bill_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE employee_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE note_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE office_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE sim_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE sim_bill_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE warehouse_id_seq INCREMENT BY 1');
        $this->addSql('ALTER TABLE employee ADD password VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER SEQUENCE address_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE note_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE office_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE activation_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE client_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE contract_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE device_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE device_bill_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE employee_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE sim_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE sim_bill_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE warehouse_id_seq INCREMENT BY 0');
        $this->addSql('ALTER TABLE employee DROP password');
    }
}
