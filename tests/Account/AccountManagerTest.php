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
        $account = $this->createAccount('incomes');
        $accountManager = $this->createAccountManager(
            accountRepository: $this->createAccountRepository($account),
        );

        $result = $accountManager->get(new AccountId('incomes'));

        $this->assertSame($result, $account);
    }

    public function testCreate(): void
    {
        $accountManager = $this->createAccountManager(
            accountIds: ['7'],
        );

        $account = $accountManager->create();

        $this->assertSame('7', $account->id->value);
    }

    public function testDelete(): void
    {
        $account1 = $this->createAccount('1');
        $account2 = $this->createAccount('2');
        $accountRepository = $this->createAccountRepository($account1, $account2);
        $accountManager = $this->createAccountManager(
            accountRepository: $accountRepository,
        );

        $accountManager->delete($account1);

        $this->assertSame([$account2], $accountRepository->getAll());
    }

    public function testDeleteWithChildren(): void
    {
        $accountIncomes = $this->createAccount('incomes');
        $accountSalary = $this->createAccount(
            'salary',
            parent: $accountIncomes
        );
        $accountRepository = $this->createAccountRepository($accountIncomes, $accountSalary);
        $accountManager = $this->createAccountManager(
            accountRepository: $accountRepository,
        );

        $this->expectException(AccountDeletionNotPossibleException::class);
        $this->expectExceptionMessage('Deletion not possible, account has children.');
        $accountManager->delete($accountIncomes);
    }

    public function testDeleteWithPostings(): void
    {
        $account = $this->createAccount('incomes');
        $accountRepository = $this->createAccountRepository($account);
        $accountManager = $this->createAccountManager(
            accountRepository: $accountRepository,
            postingRepository: $this->createPostingRepository(
                $this->createPosting(
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
