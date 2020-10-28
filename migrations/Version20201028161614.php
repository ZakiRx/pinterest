<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201028161614 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD pinid_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C5CC440AF FOREIGN KEY (pinid_id) REFERENCES pin (id)');
        $this->addSql('CREATE INDEX IDX_9474526C5CC440AF ON comment (pinid_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C5CC440AF');
        $this->addSql('DROP INDEX IDX_9474526C5CC440AF ON comment');
        $this->addSql('ALTER TABLE comment DROP pinid_id');
    }
}
