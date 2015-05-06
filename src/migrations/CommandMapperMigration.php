<?php

/**
 * @author      José Lorente <jose.lorente.martin@gmail.com>
 * @license     The MIT License (MIT)
 * @copyright   José Lorente
 * @version     1.0
 */

use yii\db\Schema;
use yii\db\Migration;

/**
 * Creates the table that handles the command list.
 * 
 * @author José Lorente <jose.lorente.martin@gmail.com>
 */
class CommandMapperMigration extends Migration {

    /**
     * @inheritdoc
     */
    public function up() {
        $this->createTable('cmd_mapper', [
            'command' => Schema::TYPE_BINARY,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER
        ]);
        $this->createIndex('INDEX_UpdatedAt', 'cmd_mapper', 'updated_at');
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable('cmd_mapper');
    }

}
