<?php

declare(strict_types=1);

namespace Cwola\Transaction;

use LogicException;

class Transaction {

    /**
     * @var int
     */
    public const TRANSACTION_OPENED = 1;

    /**
     * @var int
     */
    public const TRANSACTION_CLOSED = 0;


    /**
     * @var int
     */
    protected int $status;

    /**
     * @var \Cwola\Transaction\Record[]
     */
    protected array $transactionLog;


    /**
     * @param void
     */
    public function __construct() {
        $this->status = static::TRANSACTION_CLOSED;
        $this->transactionLog = [];
    }

    /**
     * @param void
     * @return bool
     *
     * @throws \LogicException
     */
    public function begin() :bool {
        if ($this->inTransaction()) {
            throw new LogicException('Transaction is already open.');
        }
        $this->clearTransactionLog();
        $this->status = static::TRANSACTION_OPENED;
        return true;
    }

    /**
     * @param void
     * @return bool
     */
    public function rollback() :bool {
        if (!$this->inTransaction()) {
            throw new LogicException('Transaction is not open.');
        }
        $this->clearTransactionLog();
        $this->status = static::TRANSACTION_CLOSED;
        return true;
    }

    /**
     * @param void
     * @return bool
     */
    public function commit() :bool {
        if (!$this->inTransaction()) {
            throw new LogicException('Transaction is not open.');
        }
        $this->clearTransactionLog();
        $this->status = static::TRANSACTION_CLOSED;
        return true;
    }

    /**
     * @param void
     * @return bool
     */
    public function inTransaction() :bool {
        return $this->status === static::TRANSACTION_OPENED;
    }

    /**
     * @param \Cwola\Transaction\Record? $record [optional]
     * @param array $attributes [optional]
     * @return bool
     */
    public function logging(Record $record = null, array $attributes = []) :bool {
        $xid = \count($this->transactionLog);
        if (!($record instanceof Record)) {
            $record = new Record;
        }
        $record->xid = $xid;
        $record->attributes = \array_merge($record->attributes, $attributes);
        $this->transactionLog[] = $record;
        return true;
    }
}
