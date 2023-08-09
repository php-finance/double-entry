<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Account;

use PhpFinance\DoubleEntry\Domain\Account\AccountChartId;
use PhpFinance\DoubleEntry\Domain\Account\AccountFilter;
use PHPUnit\Framework\TestCase;

final class AccountFilterTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $filter = new AccountFilter();

        $this->assertNull($filter->getAccountChartId());
    }

    public function testValues(): void
    {
        $accountChartId = new AccountChartId('test-chart-id');

        $filter = (new AccountFilter())
            ->withAccountChartId($accountChartId);

        $this->assertSame($accountChartId->value, $filter->getAccountChartId()->value);
    }

    public function testImmutability(): void
    {
        $filter = new AccountFilter();

        $this->assertNotSame($filter, $filter->withAccountChartId(new AccountChartId('test-chart-id')));
    }
}
