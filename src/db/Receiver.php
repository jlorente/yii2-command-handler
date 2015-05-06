<?php

/**
 * @author      José Lorente <jose.lorente.martin@gmail.com>
 * @license     The MIT License (MIT)
 * @copyright   José Lorente
 * @version     1.0
 */

namespace jlorente\command\db;

use yii\db\ActiveRecordInterface;
use jlorente\command\base\Receiver as BaseReceiver;

/**
 * Receiver interface for ActiveRecord objects.
 * 
 * Used along with the jlorente\command\db\Command descendant classes.
 * Receivers of the this kind of Command objects MUST implement this interface.
 * 
 * @author José Lorente <jose.lorente.martin@gmail.com>
 */
interface Receiver extends ActiveRecordInterface, BaseReceiver {
    
}
