<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Operation;

use PhpFinance\DoubleEntry\Domain\Transaction\Transaction;

final readonly class Operation
{
    /**
     * @param Transaction[] $transactions
     * @psalm-param non-empty-list<Transaction> $transactions
     */
    public function __construct(
        public OperationId $id,
        public array $transactions,
    ) {
    }
}
