<?php

/**
 * @author      José Lorente <jose.lorente.martin@gmail.com>
 * @license     The MIT License (MIT)
 * @copyright   José Lorente
 * @version     1.0
 */

namespace jlorente\notification\console\controllers;

use yii\console\Controller;
use jlorente\command\base\CommandProcessor;
use yii\helpers\Console;

/**
 * Console Controller responsible of executing the CommandProcessor.
 * 
 * @author José Lorente <jose.lorente.martin@gmail.com>
 */
class CommandProcessorController extends Controller {

    /**
     * Runs the CommandProcesor to process the n CommandMappers provided as 
     * argument or all of them if no param is specified.
     * 
     * @param int $n
     */
    public function actionRun($n = null) {
        $cProcessor = new CommandProcessor();
        if ($cProcessor->run($n) === false) {
            $this->stdout('Errors had ocurred during the mapper process phase. Please see the log in order to know more about it', Console::FG_RED);
        } else {
            $this->stdout($n . ' CommandMapper' . ($n === 1 ? '' : 's') . ' processed.', Console::FG_GREY);
        }
        echo PHP_EOL;
    }

}
