<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240512124137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE championship (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status SMALLINT NOT NULL, bidirectional TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE play (id INT AUTO_INCREMENT NOT NULL, championship_id INT NOT NULL, host_id INT NOT NULL, guest_id INT NOT NULL, host_score SMALLINT NOT NULL, guest_score SMALLINT NOT NULL, order_number SMALLINT NOT NULL, INDEX IDX_5E89DEBA94DDBCE9 (championship_id), INDEX IDX_5E89DEBA1FB8D185 (host_id), INDEX IDX_5E89DEBA9A4AA658 (guest_id), UNIQUE INDEX play_unq (championship_id, host_id, guest_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE play_off (id INT AUTO_INCREMENT NOT NULL, championship_id INT NOT NULL, team1_id INT NOT NULL, team2_id INT NOT NULL, team1_score SMALLINT NOT NULL, team2_score SMALLINT NOT NULL, stage SMALLINT NOT NULL, INDEX IDX_F59DAD8C94DDBCE9 (championship_id), INDEX IDX_F59DAD8CE72BCFA4 (team1_id), INDEX IDX_F59DAD8CF59E604A (team2_id), UNIQUE INDEX play_unq (championship_id, team1_id, team2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playing_team (id INT AUTO_INCREMENT NOT NULL, championship_id INT NOT NULL, team_id INT NOT NULL, division CHAR(1), score SMALLINT NOT NULL, position SMALLINT NOT NULL, total_position SMALLINT NOT NULL, INDEX IDX_394FCB7794DDBCE9 (championship_id), INDEX IDX_394FCB77296CD8AE (team_id), UNIQUE INDEX division_unq (championship_id, team_id, division), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE play ADD CONSTRAINT FK_5E89DEBA94DDBCE9 FOREIGN KEY (championship_id) REFERENCES championship (id)');
        $this->addSql('ALTER TABLE play ADD CONSTRAINT FK_5E89DEBA1FB8D185 FOREIGN KEY (host_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE play ADD CONSTRAINT FK_5E89DEBA9A4AA658 FOREIGN KEY (guest_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE play_off ADD CONSTRAINT FK_F59DAD8C94DDBCE9 FOREIGN KEY (championship_id) REFERENCES championship (id)');
        $this->addSql('ALTER TABLE play_off ADD CONSTRAINT FK_F59DAD8CE72BCFA4 FOREIGN KEY (team1_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE play_off ADD CONSTRAINT FK_F59DAD8CF59E604A FOREIGN KEY (team2_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE playing_team ADD CONSTRAINT FK_394FCB7794DDBCE9 FOREIGN KEY (championship_id) REFERENCES championship (id)');
        $this->addSql('ALTER TABLE playing_team ADD CONSTRAINT FK_394FCB77296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C4E0A61F5E237E06 ON team (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE play DROP FOREIGN KEY FK_5E89DEBA94DDBCE9');
        $this->addSql('ALTER TABLE play DROP FOREIGN KEY FK_5E89DEBA1FB8D185');
        $this->addSql('ALTER TABLE play DROP FOREIGN KEY FK_5E89DEBA9A4AA658');
        $this->addSql('ALTER TABLE play_off DROP FOREIGN KEY FK_F59DAD8C94DDBCE9');
        $this->addSql('ALTER TABLE play_off DROP FOREIGN KEY FK_F59DAD8CE72BCFA4');
        $this->addSql('ALTER TABLE play_off DROP FOREIGN KEY FK_F59DAD8CF59E604A');
        $this->addSql('ALTER TABLE playing_team DROP FOREIGN KEY FK_394FCB7794DDBCE9');
        $this->addSql('ALTER TABLE playing_team DROP FOREIGN KEY FK_394FCB77296CD8AE');
        $this->addSql('DROP TABLE championship');
        $this->addSql('DROP TABLE play');
        $this->addSql('DROP TABLE play_off');
        $this->addSql('DROP TABLE playing_team');
        $this->addSql('DROP INDEX UNIQ_C4E0A61F5E237E06 ON team');
    }
}
