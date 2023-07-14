<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Account;

use InvalidArgumentException;
use PhpFinance\DoubleEntry\Domain\Account\Account;
use PhpFinance\DoubleEntry\Domain\Account\AccountId;
use PhpFinance\DoubleEntry\Tests\Support\TestFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AccountTest extends TestCase
{
    public static function dataRename(): array
    {
        return [
            'base' => ['All Incomes'],
            'edge-case-min' => ['A'],
            'edge-case-max' => [str_repeat('.', 50)],
            'edge-case-max-utf8mb4' => [str_repeat('😎', 50)],
        ];
    }

    #[DataProvider('dataRename')]
    public function testRename(string $name): void
    {
        $account = TestFactory::createAccount('incomes', name: 'My Incomes');

        $account->rename($name);

        $this->assertSame($name, $account->getName());
    }

    public static function dataInvalidRename(): array
    {
        return [
            'empty-string' => [''],
            'long-string' => [str_repeat('.', 51)],
        ];
    }

    #[DataProvider('dataInvalidRename')]
    public function testInvalidRename(string $name): void
    {
        $account = TestFactory::createAccount();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Account name must be null or non-empty string no greater than 50 symbols.');
        $account->rename($name);
    }

    public function testCreateAccountWithParentFromAnotherChart(): void
    {
        $id = new AccountId('incomes');
        $parent = TestFactory::createAccount(chartId: 'clients');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Account chart of parent account is not equal to current.');
        new Account($id, parent: $parent);
    }
}
