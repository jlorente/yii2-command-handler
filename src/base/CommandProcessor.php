<?php

/**
 * @author      José Lorente <jose.lorente.martin@gmail.com>
 * @license     The MIT License (MIT)
 * @copyright   José Lorente
 * @version     1.0
 */

namespace jlorente\command\base;

use jlorente\command\db\CommandMapper;
use yii\base\InvalidParamException;
use Exception;
use Yii;
use yii\base\Object;
use SplDoublyLinkedList;
use yii\log\Logger;

/**
 * CommandProcessor processes the commands stored by the CommandMapper.
 * 
 * The processing order of the Commands can be configured.
 *
 * @author José Lorente <jose.lorente.martin@gmail.com>
 */
class CommandProcessor extends Object {

    /**
     * Mode to process the newer commands first.
     */
    const MODE_STACK = SplDoublyLinkedList::IT_MODE_LIFO;

    /**
     * Mode to process the older commands first.
     */
    const MODE_QUEUE = SplDoublyLinkedList::IT_MODE_FIFO;

    /**
     *
     * @var string Default to queue mode.
     */
    protected $mode = self::MODE_QUEUE;

    /**
     *
     * @var SplDoublyLinkedList 
     */
    protected $erroneousMappers;

    /**
     * @inheritdoc
     */
    public function init() {
        $this->erroneousMappers = new SplDoublyLinkedList();
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [['mode', 'in', 'range' => [self::MODE_QUEUE, self::MODE_STACK]]];
    }

    /**
     * Sets the process mode. It must be one on the valid constant values: 
     *  CommandMapper::MODE_QUEUE to process the older commands first.
     *  CommandMapper::MODE_STACK to process the newer commands first.
     * 
     * @param string $mode
     * @throws InvalidParamException
     */
    public function setMode($mode) {
        if ($mode !== self::MODE_QUEUE && $mode !== self::MODE_STACK) {
            throw new InvalidParamException('Invalid $mode provided');
        }
        $this->erroneousMappers->setIteratorMode($mode | SplDoublyLinkedList::IT_MODE_DELETE);
        $this->mode = $mode;
    }

    /**
     * Gets the mapper command processing mode.
     */
    public function getMode() {
        return $this->mode;
    }

    /**
     * Returns the order string for the query.
     */
    protected function getOrder() {
        return 'updated_at ' . ($this->getMode() === self::MODE_QUEUE ? 'ASC' : 'DESC');
    }

    /**
     * Runs the processor for the specified number of mappers.
     * 
     * @param int $n Number of the mappers to be processed or null for all of them.
     * @param boolean $restoreErroneousMappers Indicates whether to restore the erroneous mappers if the are any one.
     * @return false|int Boolean false if there has been any error or the number of mappers processed.
     */
    public function run($n = null, $restoreErroneousMappers = true) {
        if ($n !== null && is_numeric($n) === false) {
            throw new InvalidParamException('n must be a numeric value');
        }
        $i = 0;
        while (($n === null || $n < $i++) && ($mapper = CommandMapper::find()->orderBy($this->getOrder())->one())) {
            $this->processMapper($mapper);
        }
        if ($this->hasErroneousMappers()) {
            if ($restoreErroneousMappers) {
                $this->restoreErroneousMappers();
            }
            return false;
        } else {
            return $i;
        }
    }

    /**
     * Processes a mapper deleting it from the list and executing the command 
     * stored in it.
     * 
     * @param CommandMapper $mapper
     */
    protected function processMapper(CommandMapper $mapper) {
        $command = $mapper->getCommand();
        try {
            $mapper->delete();
            $command->execute();
        } catch (Exception $ex) {
            $this->erroneousMappers->push($mapper);
            Yii::getLogger()->log(
                    'An error ocurred while processing command [' . get_class($command) . '].'
                    . PHP_EOL
                    . 'Exception: [' . $ex->getTraceAsString() . ']', Logger::LEVEL_ERROR, 'command'
            );
        }
    }

    /**
     * Checks whether the processor has currently any erroneous mapper.
     * 
     * @return boolean
     */
    public function hasErroneousMappers() {
        return $this->erroneousMappers->count() > 0 ? true : false;
    }

    /**
     * Restores the erroneous mappers into the database.
     */
    protected function restoreErroneousMappers() {
        $this->erroneousMapper->rewind();
        while ($this->erroneousMappers->valid()) {
            $this->erroneousMappers->current()->save();
            $this->erroneousMappers->next();
        }
    }

}
