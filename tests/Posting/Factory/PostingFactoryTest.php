<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Posting\Factory;

use Brick\Money\Money;
use DateTimeImmutable;
use InvalidArgumentException;
use PhpFinance\DoubleEntry\Domain\Posting\Factory\EntryData;
use PhpFinance\DoubleEntry\Domain\Posting\Factory\PostingFactory;
use PhpFinance\DoubleEntry\Tests\Support\TestFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PostingFactoryTest extends TestCase
{
    public static function dataCreateWithDifferentAccountCharts(): array
    {
        return [
            [null, 'test'],
            ['test', null],
            ['one', 'two'],
        ];
    }

    #[DataProvider('dataCreateWithDifferentAccountCharts')]
    public function testCreateWithDifferentAccountCharts(
        ?string $debitAccountChartId,
        ?string $creditAccountChartId
    ): void {
        $factory = new PostingFactory(
            TestFactory::createPostingIdFactory(),
        );

        $date = new DateTimeImmutable();
        $amount = Money::of(100, 'RUB');
        $debitData = new EntryData(TestFactory::createAccount(chartId: $debitAccountChartId));
        $creditData = new EntryData(TestFactory::createAccount(chartId: $creditAccountChartId));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Account chart of debit and credit entries is not equal.');
        $factory->create($date, $amount, $debitData, $creditData);
    }
}
