<?php

declare(strict_types=1);

namespace Cwola\Transaction;

class Record {

    /**
     * @param int
     */
    public int $xid = 0;

    /**
     * @param array
     */
    public array $attributes = [];

}
