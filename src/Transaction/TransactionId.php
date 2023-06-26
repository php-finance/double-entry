<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Transaction;

final readonly class TransactionId
{
    public function __construct(
        public string $value
    ) {
    }
}
