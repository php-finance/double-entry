<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Posting\Factory;

use Brick\Money\Money;
use DateTimeImmutable;
use InvalidArgumentException;
use PhpFinance\DoubleEntry\Domain\Posting\Entry;
use PhpFinance\DoubleEntry\Domain\Posting\Posting;

final class PostingFactory
{
    public function __construct(
        private PostingIdFactoryInterface $postingIdFactory,
    ) {
    }

    public function create(
        DateTimeImmutable $date,
        Money $amount,
        EntryData $debitData,
        EntryData $creditData,
    ): Posting {
        if ($debitData->account->chartId?->value !== $creditData->account->chartId?->value) {
            throw new InvalidArgumentException('Account chart of debit and credit entries is not equal.');
        }

        return new Posting(
            $this->postingIdFactory->create(),
            new Entry(
                $date,
                $amount,
                $debitData->account->id,
                $debitData->dimensions,
            ),
            new Entry(
                $date,
                $amount,
                $creditData->account->id,
                $creditData->dimensions,
            )
        );
    }
}
