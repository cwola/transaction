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
     * @var string
     */
    protected string $xid;

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
        $this->xid = \bin2hex(\random_bytes(16));
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
        $rid = \count($this->transactionLog) + 1;
        if (!($record instanceof Record)) {
            $record = new Record;
        }
        $record->xid = $this->xid;
        $record->id = $rid;
        $record->attributes = \array_merge($record->attributes, $attributes);
        $this->transactionLog[] = $record;
        return true;
    }

    /**
     * @param void
     * @return bool
     */
    public function clearTransactionLog() :bool {
        $this->transactionLog = [];
        return true;
    }
}
