<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Transaction\Factory;

use Brick\Money\Money;
use PhpFinance\DoubleEntry\Domain\Posting\Factory\EntryData;

final readonly class PostingData
{
    public function __construct(
        public Money $amount,
        public EntryData $debitData,
        public EntryData $creditData,
    ) {}
}
