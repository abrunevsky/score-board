<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240515185657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE championship ADD error_message VARCHAR(255) DEFAULT NULL, CHANGE status status ENUM(\'draw\', \'play\', \'qualifying\', \'playoff_quarter\', \'playoff_semifinal\', \'playoff_final\', \'playoff_3d_place\', \'finished\', \'error\') NOT NULL');
        $this->addSql('ALTER TABLE play CHANGE status status ENUM(\'pending\', \'completed\') NOT NULL');
        $this->addSql('ALTER TABLE play_off CHANGE stage stage ENUM(\'1/4final\', \'1/2final\', \'final\', \'3place\') NOT NULL, CHANGE status status ENUM(\'pending\', \'completed\') NOT NULL');
        $this->addSql('ALTER TABLE playing_team CHANGE division division CHAR(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE championship DROP error_message, CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE playing_team CHANGE division division CHAR(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE play CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE play_off CHANGE stage stage VARCHAR(255) DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL');
    }
}
