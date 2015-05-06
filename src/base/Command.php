<?php

/**
 * @author      José Lorente <jose.lorente.martin@gmail.com>
 * @license     The MIT License (MIT)
 * @copyright   José Lorente
 * @version     1.0
 */

namespace jlorente\command\base;

/**
 * Command class is used as base class for concrete commands.
 * 
 * Provides a container for the receiver and forces it to be provided on object 
 * construction. 
 * 
 * For more details see CommandInterface.
 * 
 * @author José Lorente <jose.lorente.martin@gmail.com>
 * @see CommandInterface
 */
abstract class Command implements CommandInterface {

    /**
     *
     * @var mixed Receiver of the command.
     */
    private $receiver;

    /**
     * @inheritdoc
     */
    public function setReceiver(Receiver $receiver) {
        $this->receiver = $receiver;
    }

    /**
     * @inheritdoc
     */
    public function getReceiver() {
        return $this->receiver;
    }

}
