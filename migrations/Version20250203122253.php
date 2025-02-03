<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250203122253 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add organization field to participants table';
    }

    public function up(Schema $schema): void
    {
        // Add the organization column to the participants table
        $this->addSql('ALTER TABLE participants ADD organization VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Remove the organization column from the participants table
        $this->addSql('ALTER TABLE participants DROP organization');
    }
}
