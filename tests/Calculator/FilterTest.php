<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Calculator;

use Brick\Money\Currency;
use DateTimeImmutable;
use PhpFinance\DoubleEntry\Domain\Calculator\Filter;
use PHPUnit\Framework\TestCase;

final class FilterTest extends TestCase
{
    public function testWithCurrencies(): void
    {
        $currencies = [Currency::of('RUB'), Currency::of('USD')];

        $filter = (new Filter())->withCurrencies($currencies);

        $this->assertSame($currencies, $filter->getCurrencies());
    }

    public function testWithDateFrom(): void
    {
        $date = new DateTimeImmutable();

        $filter = (new Filter())->withDateFrom($date);

        $this->assertSame($date, $filter->getDateFrom());
    }

    public function testWithDateTo(): void
    {
        $date = new DateTimeImmutable();

        $filter = (new Filter())->withDateTo($date);

        $this->assertSame($date, $filter->getDateTo());
    }

    public function testWithDimensions(): void
    {
        $dimensions = ['contractor' => 'Mike'];

        $filter = (new Filter())->withDimensions($dimensions);

        $this->assertSame($dimensions, $filter->getDimensions());
    }

    public function testImmutable(): void
    {
        $filter = new Filter();

        $this->assertNotSame($filter, $filter->withCurrencies([]));
        $this->assertNotSame($filter, $filter->withDateFrom(null));
        $this->assertNotSame($filter, $filter->withDateTo(null));
        $this->assertNotSame($filter, $filter->withDimensions([]));
    }
}
