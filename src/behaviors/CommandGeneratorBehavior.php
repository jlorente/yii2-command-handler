<?php

/**
 * @author      José Lorente <jose.lorente.martin@gmail.com>
 * @license     The MIT License (MIT)
 * @copyright   José Lorente
 * @version     1.0
 */

namespace jlorente\command\behaviors;

use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\base\Event;
use yii\base\InvalidParamException;
use jlorente\command\base\CommandInterface;
use jlorente\command\db\CommandMapper;

/**
 * CommandGeneratorBehavior class provides a simply way to create commands 
 * based on Model events.
 * 
 * Include it as a behavior in your model indicating which command should be 
 * created and optionally add a condition to create the commands or not.
 * 
 * i.e.:
 * 
 * ```php
 * 
 * use jlorente\command\behaviors\CommandGeneratorBehavior
 * 
 * class MyModel {
 * 
 *     public function behaviors() {
 *         return [
 *             // ... other behaviors ...
 *             , 'commandGenerator' => [
 *                 , 'commands' => [
 *                     ActiveRecord::EVENT_BEFORE_VALIDATE => my\concrete\BeforeValidateCommand,
 *                     ActiveRecord::EVENT_BEFORE_INSERT => my\concrete\AfterInsertCommand,
 *                     ActiveRecord::EVENT_BEFORE_UPDATE => my\concrete\AfterUpdateCommand,
 *                 ]
 *                 , 'condition' => function($model) {
 *                     return true;
 *                 }
 *             ]
 *         ];
 *     }
 * }
 * 
 * @author José Lorente <jose.lorente.martin@gmail.com>
 */
class CommandGeneratorBehavior extends Behavior {

    /**
     * @var array List of commands used by this class attached to the events 
     * that fires the command creation. 
     * The class names must be full qualified names and all of them must 
     * implement the jlorente\command\base\CommandInterface.
     * This classes will be used by the console controller to execute the Commands.
     * 
     * @see CommandInterface
     * 
     * ```php
     * [
     *     // ... other behavior configuration ...
     *     , 'commands' => [
     *         ActiveRecord::EVENT_BEFORE_VALIDATE => my\concrete\BeforeValidateCommand,
     *         ActiveRecord::EVENT_BEFORE_INSERT => my\concrete\AfterInsertCommand,
     *         ActiveRecord::EVENT_BEFORE_UPDATE => my\concrete\AfterUpdateCommand,
     *     ]
     * ]
     * ```
     */
    public $commands = [];

    /**
     * An optional callable property that evaluates if the notifier must be 
     * created or not.
     * The callable receives the owner model as parameter. It must return a 
     * boolean indicating if the command creation should proceed or not. 
     * The owner of the behavior is passed as argument to the callable variable.
     * 
     * ```php
     * [
     *     // ... other behavior configuration ...
     *     , 'condition' => function ($model) {
     *         return true;
     *     }
     * ]
     * 
     * @var callable
     */
    public $condition;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        if (empty($this->commands)) {
            throw new InvalidConfigException('"commands" param must be provided');
        }
    }

    /**
     * @inheritdoc
     */
    public function events() {
        return array_fill_keys(array_keys($this->commands), 'createCommand');
    }

    /**
     * Generates and stores the Command object depending on the triggered event.
     * 
     * @param Event $event
     */
    public function createCommand($event) {
        if (!empty($this->commands[$event->name])) {
            if (is_callable($this->condition) && call_user_func($this->condition, $this->owner) === false) {
                return;
            }
            $class = $this->commands[$event->name];
            $command = new $class();
            if (($command instanceof CommandInterface) === false) {
                throw new InvalidParamException('Class name [' . get_class($class) . '] provided in "commands" param MUST implement the jlorente\command\base\CommandInterface interface');
            }
            $command->setReceiver($this->owner);
            CommandMapper::map($command);
        }
    }

}
