<?php

/**
 * @author      José Lorente <jose.lorente.martin@gmail.com>
 * @license     The MIT License (MIT)
 * @copyright   José Lorente
 * @version     1.0
 */

namespace jlorente\command\db;

use jlorente\command\base\Command as BaseCommand;
use jlorente\command\base\CommandInterface;
use jlorente\command\base\Receiver as BaseReceiver;

/**
 * Command class is used as base class for concrete commands created from 
 * ActiveRecord Receivers.
 * 
 * Provides a property for the receiver.
 * 
 * For more details see jlorente\command\base\CommandInterface.
 * 
 * @author José Lorente <jose.lorente.martin@gmail.com>
 * @see jlorente\command\base\CommandInterface
 */
abstract class Command extends BaseCommand implements CommandInterface {

    /**
     * @inheritdoc
     */
    public function getReceiver() {
        $wrapper = parent::getReceiver();
        if ($wrapper !== null) {
            return $wrapper->getReceiver();
        }
    }

    /**
     * @inheritdoc
     */
    public function setReceiver(BaseReceiver $receiver) {
        $wrapper = new ReceiverWrapper();
        $wrapper->setReceiver($receiver);
        parent::setReceiver($wrapper);
    }

}
