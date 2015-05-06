<?php

/**
 * @author      José Lorente <jose.lorente.martin@gmail.com>
 * @license     The MIT License (MIT)
 * @copyright   José Lorente
 * @version     1.0
 */

namespace jlorente\command\db;

use jlorente\command\base\Receiver as BaseReceiver;

/**
 * ReceiverWrapper purpose is to wrap a jlorente\command\db\Receiver object into 
 * a jlorente\command\base\Receiver in order to be stored in the 
 * jlorente\command\base\Command receiver property.
 *
 * @author José Lorente <jose.lorente.martin@gmail.com>
 */
class ReceiverWrapper implements BaseReceiver {

    /**
     *
     * @var mixed Primary key of the wrapped Receiver.
     */
    protected $pk;

    /**
     *
     * @var string Fully qualified class name of the wrapped Receiver.
     */
    protected $class;

    /**
     * Gets the Active Record receiver object wrapped in the ReceiverWrapper.
     */
    public function getReceiver() {
        $class = $this->class;
        return $class::findOne($this->pk);
    }

    /**
     * Wraps the ActiveRecord Receiver object into the ReceiverWrapper.
     */
    public function setReceiver(Receiver $receiver) {
        $this->pk = $receiver->getPrimaryKey();
        $this->class = get_class($receiver);
    }

}
