<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Transaction\Factory;

use Brick\Money\Money;
use DateTimeImmutable;
use PhpFinance\DoubleEntry\Domain\Posting\Factory\EntryData;
use PhpFinance\DoubleEntry\Domain\Posting\Factory\PostingFactory;
use PhpFinance\DoubleEntry\Domain\Transaction\Factory\PostingData;
use PhpFinance\DoubleEntry\Domain\Transaction\Factory\TransactionFactory;
use PhpFinance\DoubleEntry\Tests\Support\TestFactory;
use PhpFinance\DoubleEntry\Tests\Support\TestPostingIdFactory;
use PhpFinance\DoubleEntry\Tests\Support\TestTransactionIdFactory;
use PHPUnit\Framework\TestCase;

final class TransactionFactoryTest extends TestCase
{
    public function testBase(): void
    {
        $factory = new TransactionFactory(
            (new TestTransactionIdFactory())->setStrings('t1', 't2', 't3'),
            new PostingFactory(
                (new TestPostingIdFactory())->setStrings('p1', 'p2', 'p3'),
            ),
        );

        $date = new DateTimeImmutable('10.05.2006');
        $transaction = $factory->create(
            $date,
            new PostingData(
                Money::of(500, 'RUB'),
                new EntryData(TestFactory::createAccount('wallet')),
                new EntryData(TestFactory::createAccount('incomes')),
            ),
            new PostingData(
                Money::of(100, 'USD'),
                new EntryData(TestFactory::createAccount('expenses')),
                new EntryData(TestFactory::createAccount('wallet')),
            ),
            new PostingData(
                Money::of(300, 'IDR'),
                new EntryData(TestFactory::createAccount('wallet')),
                new EntryData(TestFactory::createAccount('incomes')),
            ),
        );

        $this->assertSame('t1', $transaction->id->value);
        $this->assertSame($date->getTimestamp(), $transaction->getDate()->getTimestamp());
        $this->assertSame([0, 1, 2], array_keys($transaction->postings));
        $this->assertSame('p1', $transaction->postings[0]->id->value);
        $this->assertSame('p2', $transaction->postings[1]->id->value);
        $this->assertSame('p3', $transaction->postings[2]->id->value);
    }
}
