<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161102102308 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('users');
        $table->addColumn('id', 'integer', [
            'autoincrement' => true,
            'unsigned' => true
        ]);
        $table->setPrimaryKey(['id']);
        $table->addColumn('name', 'string');
        $table->addColumn('email', 'string');
        $table->addColumn('password', 'string');
        $table->addColumn('salt', 'string');
        $table->addColumn('ip', 'string');
        $table->addColumn('created_at', 'datetime');
        $table->addUniqueIndex(['email']);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('users');
    }
}
