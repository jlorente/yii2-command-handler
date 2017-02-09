<?php

/**
 * @author      José Lorente <jose.lorente.martin@gmail.com>
 * @license     The MIT License (MIT)
 * @copyright   José Lorente
 * @version     1.0
 */

namespace jlorente\command\db;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use jlorente\command\base\CommandInterface;
use yii\helpers\Json;
use yii\base\Exception as YiiException;

/**
 * This is the model class for table "cmd_mapper".
 * 
 * CommandMapper persist a Command into the database in order to be executed in 
 * other process by the invoker.
 *
 * @author José Lorente <jose.lorente.martin@gmail.com>
 * @property integer $id
 * @property resource $command
 * @property integer $created_at
 * @property integer $updated_at
 */
class CommandMapper extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'jl_cmd_mapper';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['command'], 'string'],
            [['created_at', 'updated_at'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'command' => 'Command',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return array_merge(parent::behaviors(), [
            TimestampBehavior::className(),
        ]);
    }

    /**
     * @inheritdoc
     * 
     * Creates the primary key on insert.
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->id = hash('crc32b', time() . uniqid());
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * @param CommandInterface $command
     */
    public function setCommand(CommandInterface $command) {
        $this->command = serialize($command);
    }

    /**
     * 
     * @return CommandInterface
     */
    public function getCommand() {
        return unserialize($this->command);
    }

    /**
     * Stores a CommandInterface object in the database.
     * 
     * @param CommandInterface $command
     * @throws YiiException
     */
    public static function map(CommandInterface $command) {
        $mapper = new static();
        $mapper->setCommand($command);
        if ($mapper->save() === false) {
            throw new YiiException('Unable to save an instance of \jlorente\command\db\CommandMapper. Errors: [' . Json::encode($mapper->getErrors()) . ']');
        }
    }

}
