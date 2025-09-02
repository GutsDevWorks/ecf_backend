<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250902084037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA23935F83FFC');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA2399D86650F');
        $this->addSql('ALTER TABLE reservations CHANGE reminder_sent_at reminder_sent_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE reservation_status reservation_status SMALLINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA23935F83FFC FOREIGN KEY (room_id_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA2399D86650F FOREIGN KEY (user_id_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA2399D86650F');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA23935F83FFC');
        $this->addSql('ALTER TABLE reservations CHANGE reminder_sent_at reminder_sent_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE reservation_status reservation_status TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA2399D86650F FOREIGN KEY (user_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA23935F83FFC FOREIGN KEY (room_id_id) REFERENCES room (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
