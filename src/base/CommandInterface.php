<?php

/**
 * @author      José Lorente <jose.lorente.martin@gmail.com>
 * @license     The MIT License (MIT)
 * @copyright   José Lorente
 * @version     1.0
 */

namespace jlorente\command\base;

/**
 * Interface CommandInterface. 
 * 
 * Classes MUST implement this interface in order to be used as commands.
 * 
 * Remember that you should always keep in mind the fact that the receiver is 
 * the one who knows how to perform the operations needed, the purpose of the 
 * command is to help the CommandGenerator to delegate its request quickly and 
 * to make sure the command ends up where it should, so your implementation of 
 * the execute() method must not contain a complex logic, only the call to a 
 * specific method of the Receiver.
 * 
 * @author José Lorente <jose.lorente.martin@gmail.com>
 */
interface CommandInterface {

    /**
     * Executes the command.
     */
    public function execute();

    /**
     * Gets the Receiver object.
     * 
     * @return Receiver
     */
    public function getReceiver();

    /**
     * Sets the receiver object.
     * 
     * @param Receiver $receiver
     */
    public function setReceiver(Receiver $receiver);
}
