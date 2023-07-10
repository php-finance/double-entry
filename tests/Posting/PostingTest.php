<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Posting;

use Brick\Money\Money;
use DateTimeImmutable;
use PhpFinance\DoubleEntry\Domain\Posting\Exception\DimensionNotFoundException;
use PhpFinance\DoubleEntry\Domain\Posting\Factory\EntryData;
use PhpFinance\DoubleEntry\Tests\Support\TestTrait;
use PHPUnit\Framework\TestCase;

final class PostingTest extends TestCase
{
    use TestTrait;

    public function testBase(): void
    {
        $date = new DateTimeImmutable('01.01.2023');
        $amount = Money::of(200, 'USD');

        $posting = self::createPosting(
            new EntryData(self::createAccount('wallet'), ['place' => 'home']),
            new EntryData(self::createAccount('incomes'), ['type' => 'salary']),
            '7',
            $date,
            $amount
        );

        // Posting
        $this->assertSame('7', $posting->id->value);
        $this->assertSame($date, $posting->getDate());
        $this->assertSame((string) $amount, (string) $posting->getAmount());

        // Debit entry
        $this->assertSame('wallet', $posting->debit->accountId->value);
        $this->assertSame((string) $amount, (string) $posting->debit->amount);
        $this->assertSame('home', $posting->debit->getDimension('place'));
        $this->assertTrue($posting->debit->hasDimension('place'));
        $this->assertFalse($posting->debit->hasDimension('salary'));
        $this->assertFalse($posting->debit->hasDimension('non-exist'));

        // Credit entry
        $this->assertSame('incomes', $posting->credit->accountId->value);
        $this->assertSame((string) $amount, (string) $posting->credit->amount);
        $this->assertSame('salary', $posting->credit->getDimension('type'));
        $this->assertTrue($posting->credit->hasDimension('type'));
        $this->assertFalse($posting->credit->hasDimension('place'));
        $this->assertFalse($posting->credit->hasDimension('non-exist'));
    }

    public function testGetNonExistDimension(): void
    {
        $posting = self::createPosting(
            new EntryData(self::createAccount('wallet')),
            new EntryData(self::createAccount('incomes')),
        );

        $this->expectException(DimensionNotFoundException::class);
        $this->expectExceptionMessage('Entry does not have dimension "non-exist".');
        $posting->debit->getDimension('non-exist');
    }
}
