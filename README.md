Yii2 Command Handler
====================

A Yii2 extension that provides the framework with the [Command Design Pattern](http://www.oodesign.com/command-pattern.html). 
Useful to create commands in a main process and execute them in another processes such as crons, console controllers, etc.

## Installation

Include the package as dependency under the bower.json file.

To install, either run

```bash
$ php composer.phar require jlorente/yii2-command-handler "*"
```

or add

```json
...
    "require": {
        // ... other configurations ...
        "jlorente/yii2-command-handler": "*"
    }
```

to the ```require``` section of your `composer.json` file and run the following 
commands from your project directory.
```bash
$ composer update
$ ./yii migrate --migrationPath=@app/vendor/jlorente/yii2-command-handler/src/migrations
```
The last command will create the table needed to handle the command list.

## Basic Usage

First at all you must include the module in your console application in order to execute the command queue processor.

./console/config/main.php
```php
    // ... other configurations ...
    "modules" => [
        // ... other modules ...
        "command" => [
            "class" => "jlorente\command\Module"
        ]
    ]
```

Now you can begin to create your own commands.

### Example

Imagine we want multiple emails to be sent to a group of users when the admin of this group clicks a button. The process could be tedious and would increase in time the more users are in the group so our admin will have to wait until the process is finished to continue navigating over the website. 

Here, we can create a command and delegate the process to an another non locking process by writting only a few lines.

#### Receiver

First, the Group class has MUST implement \jlorente\command\base\Receiver interface and the method that will send the email to all the group users.

```php
namespace common\models;

use yii\db\ActiveRecord;
use jlorente\command\base\Receiver;

class Group extends ActiveRecord implements Receiver {

    public function sendEmailToUsers() {
        //Implementation of the send email to group users method goes here.
    }
}
```

#### Command

Now we have to create the command that will execute this method. Note that the command MUST implement the \jlorente\command\base\CommandInterface or extend one of its descendants provided along with the package in order to work (\jlorente\command\base\Command for classes that extend form \yii\base\Model and \jlorente\command\db\Command for classes that implement the \yii\db\ActiveRecordInterface).

```php
//In our example we will extend the \jlorente\command\db\Command because Group extends from \yii\db\ActiveRecord

namespace common\models\commands;

use jlorente\command\db\Command;

class GroupSendEmailCommand extends Command {

    /**
     * @inheritdoc
     */
    public function run() {
        $this->getReciver()->sendEmailToUsers();
    }
}
```

#### CommandMapper

Now we will create the controller action that receives the user click, creates and puts the command in the command list.

```php
namespace frontend\controllers;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\commands\GroupSendEmailCommand;
use jlorente\command\db\CommandMapper;

class GroupController extends Controller {
    
    public function actionSendEmail($groupId) {
        $group = Group::findOne($groupId);
        if ($group === null) {
            throw new NotFoundHttpException();
        }
        $command = new GroupSendEmailCommand();
        $command->setReceiver($group);
        CommandMapper::map($command); //Enqueues the command
    }
}
```

#### Console Controller

The console controller will process the command list and execute the commands inside it in a queue mode. You can run the console controller by using the following shell command.
```bash
$ ./yii <moduleId>/command-processor/run <int>
```
Where <moduleId> is the name you put in the module configuration of your config file and <int> is an optional argument to limit the number of mappers to be processed in the execution.

Maybe you want to put the execution of the action in a cronjob or something similar to be run every minute or in the interval that you want.

/etc/crontab
```bash
*/1 * * * * <PathToProject>/yii <moduleId>/command-processor/run
```

## Advanced Usage

#### Behavior

Along with the package comes a behavior that can be attached to your models that creates and maps command.  Continuing with the previous example, we are going to send the email to the group of users when the group changes its state property instead of having a controller action that creates and maps the command.

```php
namespace common\models;

use yii\db\ActiveRecord;
use jlorente\command\base\Receiver;
use jlorente\command\behaviors\CommandGeneratorBehavior;
use common\models\commands\GroupSendEmailCommand;

class Group extends ActiveRecord implements Receiver {

    public function sendEmailToUsers() {
        //Implementation of the send email to group users method goes here.
    }
    
    public function behaviors() {
        return array_merge(parent::behaviors(), [
            // ... other behaviors ...
            [
                'class' => CommandGeneratorBehavior::className(),
                'commands' => [
                    self::EVENT_BEFORE_SAVE => GroupSendEmailCommand::className(),
                ],
                'condition' => function ($model) {
                    return $model->isAttributeChanged('state');
                }
            ]
        ]);
    }
}
```

By doing this, the GroupSendEmailCommand will be enqueued every time the state property changes. For more configuration params see the documentation in the CommandGeneratorBehavior class.

#### CommandProcessor

Maybe you want to run the command queue processor by your own instead of using the provided console controller.

You can achieve this by instantiating and running the CommandProcessor class.

```php
use jlorente\command\base\CommandProcessor;

$processor = new CommandProcessor();
$processor->run();
```

You can set the the way that the command processor executes the commands by selecting between queue and stack mode. By default the command processor will handle the execution in queue mode.
```php
$processor->setMode(CommandProcessor::MODE_QUEUE)
```

The run method also accept two arguments. The first one is an integer that will limit the number of commands to be processed and the second is a boolean that indicates if the erroneous commands have to be restored in the queue to be processed another time. By default the processor will try to process all the commands in the list and will restore the erroneous ones.

```php
$processor->run(10, false); //This will limit the commands to 10 and won't restore the erroneous ones.
```

## License 
Copyright &copy; 2015 José Lorente Martín <jose.lorente.martin@gmail.com>.

Licensed under the MIT license. See LICENSE.txt for details.
