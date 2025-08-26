<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250826131250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE room_options (room_id INT NOT NULL, options_id INT NOT NULL, INDEX IDX_3C17466754177093 (room_id), INDEX IDX_3C1746673ADB05F1 (options_id), PRIMARY KEY(room_id, options_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE room_options ADD CONSTRAINT FK_3C17466754177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_options ADD CONSTRAINT FK_3C1746673ADB05F1 FOREIGN KEY (options_id) REFERENCES options (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room_options DROP FOREIGN KEY FK_3C17466754177093');
        $this->addSql('ALTER TABLE room_options DROP FOREIGN KEY FK_3C1746673ADB05F1');
        $this->addSql('DROP TABLE room_options');
    }
}
