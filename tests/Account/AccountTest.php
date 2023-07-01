<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Account;

use InvalidArgumentException;
use PhpFinance\DoubleEntry\Domain\Account\Account;
use PhpFinance\DoubleEntry\Domain\Account\AccountId;
use PhpFinance\DoubleEntry\Tests\Support\TestTrait;
use PHPUnit\Framework\TestCase;

final class AccountTest extends TestCase
{
    use TestTrait;

    public function testRename(): void
    {
        $account = $this->createAccount('incomes', name: 'My Incomes');

        $account->rename('All Incomes');

        $this->assertSame('All Incomes', $account->getName());
    }

    public function testRenameToEmptyString(): void
    {
        $account = $this->createAccount();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Account name must be null or non-empty string no greater than 50 symbols.');
        $account->rename('');
    }

    public function testCreateAccountWithParentFromAnotherChart(): void
    {
        $id = new AccountId('incomes');
        $parent = $this->createAccount(chartId: 'clients');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Account chart of parent account is not equal to current.');
        new Account($id, parent: $parent);
    }
}
