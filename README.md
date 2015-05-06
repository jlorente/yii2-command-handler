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

to the ```require``` section of your `composer.json` file.

## Usage

In construction

## License 
Copyright &copy; 2015 José Lorente Martín <jose.lorente.martin@gmail.com>.

Licensed under the MIT license. See LICENSE.txt for details.