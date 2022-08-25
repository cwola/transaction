<?php

declare(strict_types=1);

namespace Cwola\Transaction;

use LogicException;

use Cwola\Attribute\Readable;
use Cwola\Event;

/**
 * @property string $xid transaction id. [readonly]
 * @property int $status [readonly]
 * @property \Cwola\Transaction\Record[] $transactionLog [readonly]
 */
class Transaction implements Event\EventTarget {

    use Readable;
    use Event\EventDispatcher;

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
    #[Readable]
    protected string $xid;

    /**
     * @var int
     */
    #[Readable]
    protected int $status;

    /**
     * @var \Cwola\Transaction\Record[]
     */
    #[Readable]
    protected array $transactionLog;

    /**
     * @var bool
     */
    public bool $autoCommit;


    /**
     * @param void
     */
    public function __construct() {
        $this->xid = \bin2hex(\random_bytes(16));
        $this->status = static::TRANSACTION_CLOSED;
        $this->transactionLog = [];
        $this->autoCommit = false;
        \register_shutdown_function(function() {
            $this->shutdown();
        });
    }

    /**
     * @param void
     */
    public function __destruct() {
        $this->shutdown();
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
        $this->dispatchEvent('beginning');

        $this->clearTransactionLog();
        $this->status = static::TRANSACTION_OPENED;

        $this->dispatchEvent('begin');
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
        $this->dispatchEvent('rollbacking');

        $this->clearTransactionLog();
        $this->status = static::TRANSACTION_CLOSED;

        $this->dispatchEvent('rollback');
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
        $this->dispatchEvent('committing');

        $this->clearTransactionLog();
        $this->status = static::TRANSACTION_CLOSED;

        $this->dispatchEvent('commit');
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
    protected function clearTransactionLog() :bool {
        $this->transactionLog = [];
        return true;
    }

    /**
     * @param void
     * @return bool
     */
    protected function shutdown() :bool {
        if ($this->inTransaction()) {
            if ($this->autoCommit) {
                return $this->commit();
            }
            return $this->rollback();
        }
        return true;
    }
}
