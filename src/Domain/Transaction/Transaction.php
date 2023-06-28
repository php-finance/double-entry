<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Transaction;

use DateTimeImmutable;
use PhpFinance\DoubleEntry\Domain\Posting\Posting;
use PhpFinance\DoubleEntry\Domain\Transaction\Factory\TransactionFactory;

final readonly class Transaction
{
    /**
     * @param Posting[] $postings
     * @psalm-param non-empty-list<Posting> $postings
     *
     * Don't run constructor directly, use {@see TransactionFactory} instead of.
     */
    public function __construct(
        public TransactionId $id,
        public array $postings,
    ) {
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->postings[0]->getDate();
    }
}
