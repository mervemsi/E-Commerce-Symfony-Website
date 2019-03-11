<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181225132558 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE order_detail (id INT AUTO_INCREMENT NOT NULL, orderid INT DEFAULT NULL, userid INT DEFAULT NULL, productid INT DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, quantity INT DEFAULT NULL, amount DOUBLE PRECISION DEFAULT NULL, name VARCHAR(150) DEFAULT NULL, status VARCHAR(15) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, userid INT DEFAULT NULL, amount DOUBLE PRECISION DEFAULT NULL, name VARCHAR(20) DEFAULT NULL, address VARCHAR(150) DEFAULT NULL, city VARCHAR(15) DEFAULT NULL, phone VARCHAR(15) DEFAULT NULL, shipinfo VARCHAR(255) DEFAULT NULL, status VARCHAR(15) DEFAULT NULL, note VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user CHANGE address address VARCHAR(150) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE order_detail');
        $this->addSql('DROP TABLE orders');
        $this->addSql('ALTER TABLE user CHANGE address address VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
