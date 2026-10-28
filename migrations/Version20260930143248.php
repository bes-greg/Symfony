<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260930143248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE anime_avoir (id INT AUTO_INCREMENT NOT NULL, anime_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_9327A7C794BBE89 (anime_id), INDEX IDX_9327A7CA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE anime_vu (id INT AUTO_INCREMENT NOT NULL, anime_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_DEA190E5794BBE89 (anime_id), INDEX IDX_DEA190E5A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE anime_avoir ADD CONSTRAINT FK_9327A7C794BBE89 FOREIGN KEY (anime_id) REFERENCES anime (id)');
        $this->addSql('ALTER TABLE anime_avoir ADD CONSTRAINT FK_9327A7CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE anime_vu ADD CONSTRAINT FK_DEA190E5794BBE89 FOREIGN KEY (anime_id) REFERENCES anime (id)');
        $this->addSql('ALTER TABLE anime_vu ADD CONSTRAINT FK_DEA190E5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user DROP name');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anime_avoir DROP FOREIGN KEY FK_9327A7C794BBE89');
        $this->addSql('ALTER TABLE anime_avoir DROP FOREIGN KEY FK_9327A7CA76ED395');
        $this->addSql('ALTER TABLE anime_vu DROP FOREIGN KEY FK_DEA190E5794BBE89');
        $this->addSql('ALTER TABLE anime_vu DROP FOREIGN KEY FK_DEA190E5A76ED395');
        $this->addSql('DROP TABLE anime_avoir');
        $this->addSql('DROP TABLE anime_vu');
        $this->addSql('ALTER TABLE user ADD name VARCHAR(255) NOT NULL');
    }
}
