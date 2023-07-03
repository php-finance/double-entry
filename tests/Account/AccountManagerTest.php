<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Account;

use PhpFinance\DoubleEntry\Domain\Account\AccountId;
use PhpFinance\DoubleEntry\Domain\Account\Exception\AccountDeletionNotPossibleException;
use PhpFinance\DoubleEntry\Domain\Posting\Factory\EntryData;
use PhpFinance\DoubleEntry\Tests\Support\TestTrait;
use PHPUnit\Framework\TestCase;

final class AccountManagerTest extends TestCase
{
    use TestTrait;

    public function testGet(): void
    {
        $account = self::createAccount('incomes');
        $accountManager = self::createAccountManager(
            accountRepository: self::createAccountRepository($account),
        );

        $result = $accountManager->get(new AccountId('incomes'));

        $this->assertSame($result, $account);
    }

    public function testSave(): void
    {
        $accountRepository = self::createAccountRepository();
        $accountManager = self::createAccountManager(
            accountRepository: $accountRepository,
        );

        $account = self::createAccount('incomes');
        $accountManager->save($account);

        $this->assertSame([$account], $accountRepository->getAll());
    }

    public function testCreate(): void
    {
        $accountManager = self::createAccountManager(
            accountIds: ['7'],
        );

        $account = $accountManager->create();

        $this->assertSame('7', $account->id->value);
    }

    public function testDelete(): void
    {
        $account1 = self::createAccount('1');
        $account2 = self::createAccount('2');
        $accountRepository = self::createAccountRepository($account1, $account2);
        $accountManager = self::createAccountManager(
            accountRepository: $accountRepository,
        );

        $accountManager->delete($account1);

        $this->assertSame([$account2], $accountRepository->getAll());
    }

    public function testDeleteWithChildren(): void
    {
        $accountIncomes = self::createAccount('incomes');
        $accountSalary = self::createAccount(
            'salary',
            parent: $accountIncomes
        );
        $accountRepository = self::createAccountRepository($accountIncomes, $accountSalary);
        $accountManager = self::createAccountManager(
            accountRepository: $accountRepository,
        );

        $this->expectException(AccountDeletionNotPossibleException::class);
        $this->expectExceptionMessage('Deletion not possible, account has children.');
        $accountManager->delete($accountIncomes);
    }

    public function testDeleteWithPostings(): void
    {
        $account = self::createAccount('incomes');
        $accountRepository = self::createAccountRepository($account);
        $accountManager = self::createAccountManager(
            accountRepository: $accountRepository,
            postingRepository: self::createPostingRepository(
                self::createPosting(
                    new EntryData($account),
                    new EntryData($account),
                ),
            ),
        );

        $this->expectException(AccountDeletionNotPossibleException::class);
        $this->expectExceptionMessage('Deletion not possible, entries with account exists.');
        $accountManager->delete($account);
    }
}
