<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211115132513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE colors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, hex VARCHAR(128) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE files (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(128) NOT NULL, INDEX IDX_6354059A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE files_palettes (files_id INT NOT NULL, palettes_id INT NOT NULL, INDEX IDX_59DA6FDFA3E65B2F (files_id), INDEX IDX_59DA6FDFB01000BC (palettes_id), PRIMARY KEY(files_id, palettes_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE palettes (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(128) DEFAULT NULL, nbr_likes INT DEFAULT NULL, public TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', features TINYINT(1) DEFAULT NULL, INDEX IDX_AC7476977E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE palettes_user (palettes_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_AC8E917BB01000BC (palettes_id), INDEX IDX_AC8E917BA76ED395 (user_id), PRIMARY KEY(palettes_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE palettes_colors (palettes_id INT NOT NULL, colors_id INT NOT NULL, INDEX IDX_C77662C0B01000BC (palettes_id), INDEX IDX_C77662C05C002039 (colors_id), PRIMARY KEY(palettes_id, colors_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE themes (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE themes_palettes (themes_id INT NOT NULL, palettes_id INT NOT NULL, INDEX IDX_19160A2494F4A9D2 (themes_id), INDEX IDX_19160A24B01000BC (palettes_id), PRIMARY KEY(themes_id, palettes_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(128) NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE files ADD CONSTRAINT FK_6354059A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE files_palettes ADD CONSTRAINT FK_59DA6FDFA3E65B2F FOREIGN KEY (files_id) REFERENCES files (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE files_palettes ADD CONSTRAINT FK_59DA6FDFB01000BC FOREIGN KEY (palettes_id) REFERENCES palettes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE palettes ADD CONSTRAINT FK_AC7476977E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE palettes_user ADD CONSTRAINT FK_AC8E917BB01000BC FOREIGN KEY (palettes_id) REFERENCES palettes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE palettes_user ADD CONSTRAINT FK_AC8E917BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE palettes_colors ADD CONSTRAINT FK_C77662C0B01000BC FOREIGN KEY (palettes_id) REFERENCES palettes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE palettes_colors ADD CONSTRAINT FK_C77662C05C002039 FOREIGN KEY (colors_id) REFERENCES colors (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE themes_palettes ADD CONSTRAINT FK_19160A2494F4A9D2 FOREIGN KEY (themes_id) REFERENCES themes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE themes_palettes ADD CONSTRAINT FK_19160A24B01000BC FOREIGN KEY (palettes_id) REFERENCES palettes (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE palettes_colors DROP FOREIGN KEY FK_C77662C05C002039');
        $this->addSql('ALTER TABLE files_palettes DROP FOREIGN KEY FK_59DA6FDFA3E65B2F');
        $this->addSql('ALTER TABLE files_palettes DROP FOREIGN KEY FK_59DA6FDFB01000BC');
        $this->addSql('ALTER TABLE palettes_user DROP FOREIGN KEY FK_AC8E917BB01000BC');
        $this->addSql('ALTER TABLE palettes_colors DROP FOREIGN KEY FK_C77662C0B01000BC');
        $this->addSql('ALTER TABLE themes_palettes DROP FOREIGN KEY FK_19160A24B01000BC');
        $this->addSql('ALTER TABLE themes_palettes DROP FOREIGN KEY FK_19160A2494F4A9D2');
        $this->addSql('ALTER TABLE files DROP FOREIGN KEY FK_6354059A76ED395');
        $this->addSql('ALTER TABLE palettes DROP FOREIGN KEY FK_AC7476977E3C61F9');
        $this->addSql('ALTER TABLE palettes_user DROP FOREIGN KEY FK_AC8E917BA76ED395');
        $this->addSql('DROP TABLE colors');
        $this->addSql('DROP TABLE files');
        $this->addSql('DROP TABLE files_palettes');
        $this->addSql('DROP TABLE palettes');
        $this->addSql('DROP TABLE palettes_user');
        $this->addSql('DROP TABLE palettes_colors');
        $this->addSql('DROP TABLE themes');
        $this->addSql('DROP TABLE themes_palettes');
        $this->addSql('DROP TABLE user');
    }
}
