<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Transaction;

use PhpFinance\DoubleEntry\Domain\Transaction\TransactionId;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TransactionIdTest extends TestCase
{
    public function testBase(): void
    {
        $id = new TransactionId('42');

        $this->assertSame('42', $id->value);
    }

    public static function dataIsEqualTo(): array
    {
        return [
            'equal' => [
                true,
                new TransactionId('7'),
                new TransactionId('7'),
            ],
            'non-equal' => [
                false,
                new TransactionId('7'),
                new TransactionId('42'),
            ],
        ];
    }

    #[DataProvider('dataIsEqualTo')]
    public function testIsEqualTo(bool $expected, TransactionId $id1, TransactionId $id2): void
    {
        $this->assertSame($expected, $id1->isEqualTo($id2));
    }
}
