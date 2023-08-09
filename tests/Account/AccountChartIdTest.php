<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Account;

use PhpFinance\DoubleEntry\Domain\Account\AccountChartId;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AccountChartIdTest extends TestCase
{
    public static function dataIsEqualTo(): array
    {
        return [
            'equal' => [
                true,
                new AccountChartId('7'),
                new AccountChartId('7'),
            ],
            'non-equal' => [
                false,
                new AccountChartId('7'),
                new AccountChartId('42'),
            ],
        ];
    }

    #[DataProvider('dataIsEqualTo')]
    public function testIsEqualTo(bool $expected, AccountChartId $id1, AccountChartId $id2): void
    {
        $this->assertSame($expected, $id1->isEqualTo($id2));
    }
}
