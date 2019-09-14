# NPM Parser for UNPAR

![Packagist](https://img.shields.io/packagist/l/chez14/unpar-npm-parser) 

UNPAR (Parahyangan Catholic University) recently got their system updated, so things like NPM got changed too. This will helps to parse the weirdness of those NPMs.

## Features

### Automaticly Parse NPM

Despite of your enrollment year, we can distinguish your NPM format.

Example, my NPM, `2016730011`:

```php
$npm_info = \Chez14\NpmParser\Solver::getInfo("2016730011");
var_dump($npm_info);
/*
array(7) {
  ["enrollment_year"]=>
  string(4) "2016"
  ["jurusan_id"]=>
  string(3) "730"
  ["npm"]=>
  string(3) "011"
  ["jurusan"]=>
  string(18) "Teknik Informatika"
  ["fakultas_id"]=>
  string(1) "7"
  ["fakultas"]=>
  string(29) "Teknologi Informasi dan Sains"
  ["jenjang"]=>
  string(2) "S1"
}
*/
```

## System Requirement

- PHP v7.2 or later.

## Installation

### Composer

Execute this:

```shell
$ composer require chez14/unpar-npm-parser
```

### Manual

Include these files, please make sure they're sorted as is:

```php
require 'src/exception/badenrollmentyear.php';
require 'src/exception/notparseable.php';
require 'src/solverinterface.php';
require 'src/solvers/npm1955.php';
require 'src/solvers/npm2018.php';
require 'src/npmmodel.php';
require 'src/solver.php';
```

## API Info

TBD

## License

[MIT](LICENSE).