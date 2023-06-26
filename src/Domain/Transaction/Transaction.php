<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Transaction;

use Brick\Money\Money;
use DateTimeImmutable;
use PhpFinance\DoubleEntry\Domain\Account\AccountId;

final readonly class Transaction
{
    public function __construct(
        public TransactionId $transactionId,
        public DateTimeImmutable $date,
        public Money $amount,
        public AccountId $debitAccountId,
        public AccountId $creditAccountId,
    ) {
    }
}
