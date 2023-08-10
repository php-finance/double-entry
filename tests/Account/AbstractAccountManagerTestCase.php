<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Account;

use PhpFinance\DoubleEntry\Domain\Account\Account;
use PhpFinance\DoubleEntry\Domain\Account\AccountChartId;
use PhpFinance\DoubleEntry\Domain\Account\AccountFilter;
use PhpFinance\DoubleEntry\Domain\Account\AccountId;
use PhpFinance\DoubleEntry\Domain\Account\AccountManager;
use PhpFinance\DoubleEntry\Domain\Account\AccountRepositoryInterface;
use PhpFinance\DoubleEntry\Domain\Account\Exception\AccountDeletionNotPossibleException;
use PhpFinance\DoubleEntry\Domain\Posting\Factory\EntryData;
use PhpFinance\DoubleEntry\Domain\Posting\Posting;
use PhpFinance\DoubleEntry\Domain\Posting\PostingRepositoryInterface;
use PhpFinance\DoubleEntry\Tests\Support\TestFactory;
use PHPUnit\Framework\Attributes\DataProvider;
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

    public function testFind(): void
    {
        $accountManager = $this->createAccountManager(
            accountRepository: $this->createAccountRepository(
                TestFactory::createAccount('id1', 'chartA'),
                TestFactory::createAccount('id2', 'chartB'),
                TestFactory::createAccount('id3', 'chartA'),
            ),
        );

        $resultAccountIds = array_map(
            static fn(Account $account) => $account->id->value,
            $accountManager->find(),
        );

        $this->assertSame(['id1', 'id2', 'id3'], $resultAccountIds);
    }

    public static function dataFindWithFilter(): array
    {
        return [
            [['id1', 'id2', 'id3', 'id4', 'id5', 'id6', 'id7'], null],
            [['id1', 'id2', 'id3', 'id4', 'id5', 'id6', 'id7'], new AccountFilter()],
            [['id1', 'id3', 'id4', 'id7'], (new AccountFilter())->withAccountChartId(new AccountChartId('chartA'))],
            [['id6'], (new AccountFilter())->withAccountChartId(new AccountChartId('chartC'))],
            [[], (new AccountFilter())->withAccountChartId(new AccountChartId('chartNotExist'))],
        ];
    }

    #[DataProvider('dataFindWithFilter')]
    public function testFindWithFilter(array $expectedAccountIds, ?AccountFilter $filter): void
    {
        $accountManager = $this->createAccountManager(
            accountRepository: $this->createAccountRepository(
                TestFactory::createAccount('id1', 'chartA'),
                TestFactory::createAccount('id2', 'chartB'),
                TestFactory::createAccount('id3', 'chartA'),
                TestFactory::createAccount('id4', 'chartA'),
                TestFactory::createAccount('id5', 'chartB'),
                TestFactory::createAccount('id6', 'chartC'),
                TestFactory::createAccount('id7', 'chartA'),
            ),
        );

        $resultAccountIds = array_map(
            static fn(Account $account) => $account->id->value,
            $accountManager->find($filter),
        );

        $this->assertSame($expectedAccountIds, $resultAccountIds);
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
        $accountRepository = $this->createAccountRepository();
        $accountManager = $this->createAccountManager(
            newAccountIds: ['7'],
            accountRepository: $accountRepository,
        );

        $account = $accountManager->create();

        $this->assertSame('7', $account->id->value);
        $this->assertTrue($accountRepository->exists(new AccountId('7')));
    }

    public function testExists(): void
    {
        $accountManager = $this->createAccountManager(
            accountRepository: $this->createAccountRepository(
                TestFactory::createAccount('1'),
                TestFactory::createAccount('2'),
            ),
        );

        $this->assertTrue($accountManager->exists(new AccountId('1')));
        $this->assertTrue($accountManager->exists(new AccountId('2')));
        $this->assertFalse($accountManager->exists(new AccountId('3')));
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

    public function testDeleteWithChildrenAndNamedAccounts(): void
    {
        $accountIncomes = TestFactory::createAccount('acc1', name: 'Incomes');
        $accountSalary = TestFactory::createAccount(
            'salary',
            parent: $accountIncomes
        );
        $accountRepository = $this->createAccountRepository($accountIncomes, $accountSalary);
        $accountManager = $this->createAccountManager(
            accountRepository: $accountRepository,
        );

        $this->expectException(AccountDeletionNotPossibleException::class);
        $this->expectExceptionMessage('Deletion not possible, account "Incomes" has children.');
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

    public function testDeleteWithPostingsAndNamedAccounts(): void
    {
        $account = TestFactory::createAccount('acc1', name: 'Incomes');
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
        $this->expectExceptionMessage('Deletion not possible, entries with account "Incomes" exists.');
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
