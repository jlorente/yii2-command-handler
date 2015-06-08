Yii2 Command Handler (Development Phase)
========================================

A Yii2 extension that provides the framework with the [Command Design Pattern](http://www.oodesign.com/command-pattern.html). 
Useful to create commands in a main process and execute them in another processes 
such as crons, console controllers, etc.

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

## Usage

First at all you must incluide the module in your console application in order to execute the command queue processor.

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

Imagine you want multiple emails to be sent to a group of users when the admin of this group clicks a botton. The process could be tedious and would increase in time the more users are in the group. So, our admin will have to wait until the process is finished to continue navigating over the website. 

Here we can create a command and delegate the process to an another non locking process by writting only a few lines.

First, we must create the method in the Group model that sends the email to all the group users.

```php

class Group extends \yii\db\ActiveRecord {

    public function sendEmailToUsers() {
        //Implementation of the send email to group users method goes here.
    }
}
```

Now we must create the command that will execute this method. Note that the command must implement the \jlorente\command\base\CommandInterface or extend one of its descendants provided with the package in order to work (\jlorente\command\base\Command for classes that extend form \yii\base\Model and \jlorente\command\db\Command for classes that implement the \yii\db\ActiveRecordInterface).

```php
//In our example we will extend the \jlorente\command\db\Command because Group extends from \yii\db\ActiveRecord

class GroupSendEmailCommand extends \jlorente\command\db\Command {

    /**
     * @inheritdoc
     */
    public function run() {
        $this->getReciver()->sendEmailToUsers();
    }
}
```

The Group class, as receiver of the command, MUST implement the \jlorente\command\base\Receiver interface.

```php

class Group extends \yii\db\ActiveRecord implements \jlorente\command\base\Receiver {
    //Class body
}
```

## License 
Copyright &copy; 2015 José Lorente Martín <jose.lorente.martin@gmail.com>.

Licensed under the MIT license. See LICENSE.txt for details.
