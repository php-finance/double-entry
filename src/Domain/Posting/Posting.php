<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Posting;

use Brick\Money\Money;
use DateTimeImmutable;
use PhpFinance\DoubleEntry\Domain\Posting\Factory\PostingFactory;

final readonly class Posting
{
    /**
     * Don't run constructor directly, use {@see PostingFactory} instead of.
     */
    public function __construct(
        public PostingId $id,
        public Entry $debit,
        public Entry $credit
    ) {
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->debit->date;
    }

    public function getAmount(): Money
    {
        return $this->debit->amount;
    }
}
