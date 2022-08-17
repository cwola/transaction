# Transaction

Simple transaction modules.

## Overview

"Really simple" transaction modules.

This module is not intended to be used by itself.

## Requirement
- PHP7.0+

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

## Licence

[MIT](https://github.com/cwola/transaction/blob/main/LICENSE)
