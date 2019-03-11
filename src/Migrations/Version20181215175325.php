<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181215175325 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE messages (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) DEFAULT NULL, email VARCHAR(50) DEFAULT NULL, subject VARCHAR(100) DEFAULT NULL, message VARCHAR(255) DEFAULT NULL, comment VARCHAR(100) DEFAULT NULL, status VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE slider');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE slider (Id INT AUTO_INCREMENT NOT NULL, image VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci, title VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci, description VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci, status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci, product_id VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci, PRIMARY KEY(Id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE messages');
    }
}
