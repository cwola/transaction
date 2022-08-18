<?php

declare(strict_types=1);

namespace Cwola\Transaction;

class Record {

    /**
     * @param string
     */
    public string $xid = '';

    /**
     * @param int
     */
    public int $id = 0;

    /**
     * @param array
     */
    public array $attributes = [];

}
