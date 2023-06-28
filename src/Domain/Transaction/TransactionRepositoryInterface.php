<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Transaction;

use PhpFinance\DoubleEntry\Domain\Transaction\Exception\TransactionNotFoundException;

interface TransactionRepositoryInterface
{
    /**
     * @throws TransactionNotFoundException
     */
    public function get(TransactionId $id): Transaction;

    public function exists(TransactionId $id): bool;

    public function save(Transaction $transaction): void;
}
