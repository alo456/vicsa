<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181019063426 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER SEQUENCE activation_id_seq INCREMENT BY 1');
        $this->addSql('ALTER SEQUENCE address_id_seq INCREMENT BY 1');
        $this->addSql('CREATE SEQUENCE client_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE contract_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE device_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE device_bill_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE employee_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE note_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE office_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sim_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sim_bill_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE warehouse_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE activation ADD CONSTRAINT FK_1C68607726ED0855 FOREIGN KEY (note_id) REFERENCES note (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE activation ADD CONSTRAINT FK_1C68607794A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE activation ADD CONSTRAINT FK_1C686077F81AF80C FOREIGN KEY (sim_id) REFERENCES sim (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F285919EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F28598C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F2859116B3934 FOREIGN KEY (activation_id) REFERENCES activation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68E5080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68E903ACB68 FOREIGN KEY (device_bill_id) REFERENCES device_bill (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68E26ED0855 FOREIGN KEY (note_id) REFERENCES note (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE office ADD CONSTRAINT FK_74516B02F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sim ADD CONSTRAINT FK_2ECAC2105080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sim ADD CONSTRAINT FK_2ECAC210877FE0B6 FOREIGN KEY (sim_bill_id) REFERENCES sim_bill (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sim ADD CONSTRAINT FK_2ECAC21026ED0855 FOREIGN KEY (note_id) REFERENCES note (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE warehouse ADD CONSTRAINT FK_ECB38BFCFFA0C224 FOREIGN KEY (office_id) REFERENCES office (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER SEQUENCE address_id_seq INCREMENT BY 0');
        $this->addSql('ALTER SEQUENCE activation_id_seq INCREMENT BY 0');
        $this->addSql('DROP SEQUENCE client_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE contract_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE device_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE device_bill_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE employee_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE note_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE office_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sim_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sim_bill_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE warehouse_id_seq CASCADE');
        $this->addSql('ALTER TABLE activation DROP CONSTRAINT FK_1C68607726ED0855');
        $this->addSql('ALTER TABLE activation DROP CONSTRAINT FK_1C68607794A4C7D4');
        $this->addSql('ALTER TABLE activation DROP CONSTRAINT FK_1C686077F81AF80C');
        $this->addSql('ALTER TABLE client DROP CONSTRAINT FK_C7440455F5B7AF75');
        $this->addSql('ALTER TABLE contract DROP CONSTRAINT FK_E98F285919EB6921');
        $this->addSql('ALTER TABLE contract DROP CONSTRAINT FK_E98F28598C03F15C');
        $this->addSql('ALTER TABLE contract DROP CONSTRAINT FK_E98F2859116B3934');
        $this->addSql('ALTER TABLE device DROP CONSTRAINT FK_92FB68E5080ECDE');
        $this->addSql('ALTER TABLE device DROP CONSTRAINT FK_92FB68E903ACB68');
        $this->addSql('ALTER TABLE device DROP CONSTRAINT FK_92FB68E26ED0855');
        $this->addSql('ALTER TABLE office DROP CONSTRAINT FK_74516B02F5B7AF75');
        $this->addSql('ALTER TABLE sim DROP CONSTRAINT FK_2ECAC2105080ECDE');
        $this->addSql('ALTER TABLE sim DROP CONSTRAINT FK_2ECAC210877FE0B6');
        $this->addSql('ALTER TABLE sim DROP CONSTRAINT FK_2ECAC21026ED0855');
        $this->addSql('ALTER TABLE warehouse DROP CONSTRAINT FK_ECB38BFCFFA0C224');
    }
}
