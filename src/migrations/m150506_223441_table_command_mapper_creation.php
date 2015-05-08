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
 * To apply this migration run:
 * ```bash
 * $ ./yii migrate --migrationPath=@app/vendor/jlorente/yii2-command-handler/src/migrations
 * ```
 * 
 * @author José Lorente <jose.lorente.martin@gmail.com>
 */
class m150506_223441_table_command_mapper_creation extends Migration {

    /**
     * @inheritdoc
     */
    public function up() {
        $this->createTable('cmd_mapper', [
            'id' => Schema::TYPE_STRING.'(8) NOT NULL',
            'command' => Schema::TYPE_BINARY,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER
        ]);
        $this->addPrimaryKey('PK_CmdMapper_Id', 'cmd_mapper', 'id');
        $this->createIndex('INDEX_UpdatedAt', 'cmd_mapper', 'updated_at');
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable('cmd_mapper');
    }

}
