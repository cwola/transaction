# Transaction

Simple transaction modules.

## Overview

"Really simple" transaction modules.

This module is not intended to be used by itself.

## Requirement
- PHP8.0+
- [cwola/attribute](https://packagist.org/packages/cwola/attribute)
- [cwola/event](https://packagist.org/packages/cwola/event)

## Installation
```
composer require cwola/transaction
```

## Usage
```
<?php

use Cwola\Transaction;

$transaction = new Transaction\Transaction;
$transaction->begin();

echo $transaction->inTransaction()
    ? 'OPENED' : 'CLOSED';
// output : OPENED


$transaction->commit();

echo $transaction->inTransaction()
    ? 'OPENED' : 'CLOSED';
// output : CLOSED

```

## Add event
```
<?php

use Cwola\Transaction;
use Cwola\Event;

$transaction = new Transaction\Transaction;

$transaction->addEventListener('beginning', function() {
    echo 'beginning... : ';
});
$transaction->addEventListener('begin', function(Event\Event $event) {
    echo sprintf('SUCCESS %s', $event->timeStamp) . PHP_EOL;
});

$transaction->addEventListener('rollbacking', function() {
    echo 'rollbacking... : ';
});
$transaction->addEventListener('rollback', function(Event\Event $event) {
    echo sprintf('SUCCESS %s', $event->timeStamp) . PHP_EOL;
});

$transaction->addEventListener('committing', function() {
    echo 'committing... : ';
});
$transaction->addEventListener('commit', function(Event\Event $event) {
    echo sprintf('SUCCESS %s', $event->timeStamp) . PHP_EOL;
});

$transaction->begin();
// output
// beginning... : SUCCESS 2022-08-22T23:32:51+09:00

echo $transaction->inTransaction()
    ? 'OPENED' : 'CLOSED';
// output : OPENED


$transaction->commit();
// output
// committing... : SUCCESS 2022-08-22T23:32:51+09:00

echo $transaction->inTransaction()
    ? 'OPENED' : 'CLOSED';
// output : CLOSED

```

## Licence

[MIT](https://github.com/cwola/transaction/blob/main/LICENSE)
