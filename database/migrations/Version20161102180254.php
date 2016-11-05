<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161102180254 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('user_tokens');
        $table->addColumn('id', 'integer', [
            'autoincrement' => true
        ]);
        $table->setPrimaryKey(['id']);
        $table->addColumn('user_id', 'integer', [
            'unsigned' => true
        ]);
        $table->addColumn('token', 'string');
        $table->addColumn('device', 'string');
        $table->addColumn('created_at', 'datetime');
        $table->addForeignKeyConstraint('users', ['user_id'], ['id'], [
            'onUpdate' => 'CASCADE',
            'onDelete' => 'RESTRICT',
            'onInsert' => 'RESTRICT'
        ]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('user_tokens');
    }
}
