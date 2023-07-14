<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Account;

use PhpFinance\DoubleEntry\Domain\Account\Account;
use PhpFinance\DoubleEntry\Domain\Account\AccountId;
use PhpFinance\DoubleEntry\Domain\Account\AccountManager;
use PhpFinance\DoubleEntry\Domain\Account\AccountRepositoryInterface;
use PhpFinance\DoubleEntry\Domain\Account\Exception\AccountDeletionNotPossibleException;
use PhpFinance\DoubleEntry\Domain\Posting\Factory\EntryData;
use PhpFinance\DoubleEntry\Domain\Posting\Posting;
use PhpFinance\DoubleEntry\Domain\Posting\PostingRepositoryInterface;
use PhpFinance\DoubleEntry\Tests\Support\TestFactory;
use PHPUnit\Framework\TestCase;

abstract class AbstractAccountManagerTestCase extends TestCase
{
    public function testGet(): void
    {
        $account = TestFactory::createAccount('incomes');
        $accountManager = $this->createAccountManager(
            accountRepository: $this->createAccountRepository($account),
        );

        $result = $accountManager->get(new AccountId('incomes'));

        $this->assertSame($result, $account);
    }

    public function testSave(): void
    {
        $accountRepository = $this->createAccountRepository();
        $accountManager = $this->createAccountManager(
            accountRepository: $accountRepository,
        );

        $account = TestFactory::createAccount('incomes');
        $accountManager->save($account);

        $this->assertSame([$account], $accountRepository->getAll());
    }

    public function testCreate(): void
    {
        $accountManager = $this->createAccountManager(
            newAccountIds: ['7'],
        );

        $account = $accountManager->create();

        $this->assertSame('7', $account->id->value);
    }

    public function testDelete(): void
    {
        $account1 = TestFactory::createAccount('1');
        $account2 = TestFactory::createAccount('2');
        $accountRepository = $this->createAccountRepository($account1, $account2);
        $accountManager = $this->createAccountManager(
            accountRepository: $accountRepository,
        );

        $accountManager->delete($account1);

        $this->assertSame([$account2], $accountRepository->getAll());
    }

    public function testDeleteWithChildren(): void
    {
        $accountIncomes = TestFactory::createAccount('incomes');
        $accountSalary = TestFactory::createAccount(
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
        $account = TestFactory::createAccount('incomes');
        $accountRepository = $this->createAccountRepository($account);
        $accountManager = $this->createAccountManager(
            accountRepository: $accountRepository,
            postingRepository: $this->createPostingRepository(
                TestFactory::createPosting(
                    new EntryData($account),
                    new EntryData($account),
                ),
            ),
        );

        $this->expectException(AccountDeletionNotPossibleException::class);
        $this->expectExceptionMessage('Deletion not possible, entries with account exists.');
        $accountManager->delete($account);
    }

    abstract protected function createAccountRepository(Account ...$accounts): AccountRepositoryInterface;

    abstract protected function createPostingRepository(Posting ...$postings): PostingRepositoryInterface;

    /**
     * @param string[] $newAccountIds
     */
    private function createAccountManager(
        array $newAccountIds = [],
        ?AccountRepositoryInterface $accountRepository = null,
        ?PostingRepositoryInterface $postingRepository = null,
    ): AccountManager {
        return new AccountManager(
            $accountRepository ?? $this->createAccountRepository(),
            TestFactory::createAccountIdFactory($newAccountIds),
            $postingRepository ?? $this->createPostingRepository(),
        );
    }
}
