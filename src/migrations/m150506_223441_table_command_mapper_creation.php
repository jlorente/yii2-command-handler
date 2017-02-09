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
        try {
            $this->upgradePackage();
        } catch (Exception $ex) {
            $this->createPackage();
        }
        $this->createIndex($this->getIndexName(), $this->getTableName(), 'updated_at');
    }

    /**
     * Table name modification. Only for upgrading from previous versions.
     */
    protected function upgradePackage() {
        $this->renameTable('cmd_mapper', $this->getTableName());
        $this->dropIndex('INDEX_UpdatedAt');
    }

    /**
     * @inheritdoc
     */
    public function createPackage() {
        $this->createTable($this->getTableName(), [
            'id' => Schema::TYPE_STRING . '(8) NOT NULL',
            'command' => Schema::TYPE_BINARY . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER
        ]);
        $this->addPrimaryKey('PK_CmdMapper_Id', 'cmd_mapper', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable($this->getTableName());
    }

    /**
     * Returns the table name of the variable model. You can override this 
     * method in order to provide a custom table name.
     * 
     * @return string
     */
    protected function getTableName() {
        return 'jl_cmd_mapper';
    }

    /**
     * Returns the index name of the command mapper model. You can override this 
     * method in order to provide a custom table name.
     * 
     * @return string
     */
    protected function getIndexName() {
        return 'INDEX_JlCmdMapper_UpdatedAt';
    }

}
