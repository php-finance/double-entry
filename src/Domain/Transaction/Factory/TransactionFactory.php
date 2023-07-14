<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Transaction\Factory;

use DateTimeImmutable;
use PhpFinance\DoubleEntry\Domain\Posting\Factory\PostingFactory;
use PhpFinance\DoubleEntry\Domain\Transaction\Transaction;

final class TransactionFactory
{
    public function __construct(
        private TransactionIdFactoryInterface $transactionIdFactory,
        private PostingFactory $postingFactory,
    ) {
    }

    public function create(
        DateTimeImmutable $date,
        PostingData $postingData,
        PostingData ...$postingsData,
    ): Transaction {
        $postings = [];
        foreach ([$postingData, ...$postingsData] as $data) {
            $postings[] = $this->postingFactory->create($date, $data->amount, $data->debitData, $data->creditData);
        }

        return new Transaction(
            $this->transactionIdFactory->create(),
            $postings,
        );
    }
}
