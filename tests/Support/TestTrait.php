<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Support;

use Brick\Math\BigRational;
use Brick\Money\MoneyBag;

trait TestTrait
{
    protected function assertSameMoneyBags(MoneyBag $expectedMoneyBag, MoneyBag $moneyBag): void
    {
        $prepareFn = static fn(BigRational $value) => (string) $value->simplified();
        $expectedAmounts = array_map($prepareFn, $expectedMoneyBag->getAmounts());
        $amounts = array_map($prepareFn, $moneyBag->getAmounts());
        static::assertEquals($expectedAmounts, $amounts);
    }
}
