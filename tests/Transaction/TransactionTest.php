<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Transaction;

use DateTimeImmutable;
use PhpFinance\DoubleEntry\Domain\Posting\Factory\EntryData;
use PhpFinance\DoubleEntry\Domain\Transaction\Transaction;
use PhpFinance\DoubleEntry\Domain\Transaction\TransactionId;
use PhpFinance\DoubleEntry\Tests\Support\TestFactory;
use PHPUnit\Framework\TestCase;

final class TransactionTest extends TestCase
{
    public function testBase(): void
    {
        $date = new DateTimeImmutable('11.11.2011');
        $transaction = new Transaction(
            new TransactionId('7'),
            [
                TestFactory::createPosting(
                    new EntryData(TestFactory::createAccount('wallet')),
                    new EntryData(TestFactory::createAccount('incomes')),
                    'id1',
                    $date,
                ),
                TestFactory::createPosting(
                    new EntryData(TestFactory::createAccount('wallet')),
                    new EntryData(TestFactory::createAccount('incomes')),
                    'id2',
                    $date,
                ),
            ]
        );

        $this->assertSame('7', $transaction->id->value);
        $this->assertSame($date->getTimestamp(), $transaction->getDate()->getTimestamp());
        $this->assertSame([0, 1], array_keys($transaction->postings));
        $this->assertSame('id1', $transaction->postings[0]->id->value);
        $this->assertSame('id2', $transaction->postings[1]->id->value);
    }
}
