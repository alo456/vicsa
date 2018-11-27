<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181125031810 extends AbstractMigration
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
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C74404554F2899EF ON client (rfc)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_92FB68E5E4805E4 ON device (mat_key)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F318CCD914F702F8 ON device_bill (doc_number)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5D9F75A1E7927C74 ON employee (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2ECAC2105E4805E4 ON sim (mat_key)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C9FC24D14F702F8 ON sim_bill (doc_number)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER SEQUENCE address_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE note_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE office_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE device_bill_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE activation_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE client_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE contract_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE device_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE employee_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE sim_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE sim_bill_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE warehouse_id_seq INCREMENT BY 0');
        $this->addSql('DROP INDEX UNIQ_C9FC24D14F702F8');
        $this->addSql('DROP INDEX UNIQ_F318CCD914F702F8');
        $this->addSql('DROP INDEX UNIQ_C74404554F2899EF');
        $this->addSql('DROP INDEX UNIQ_92FB68E5E4805E4');
        $this->addSql('DROP INDEX UNIQ_2ECAC2105E4805E4');
        $this->addSql('DROP INDEX UNIQ_5D9F75A1E7927C74');
    }
}
