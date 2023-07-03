<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Support;

use Brick\Math\BigRational;
use Brick\Money\Money;
use Brick\Money\MoneyBag;
use DateTimeImmutable;
use PhpFinance\DoubleEntry\Domain\Account\Account;
use PhpFinance\DoubleEntry\Domain\Account\AccountChartId;
use PhpFinance\DoubleEntry\Domain\Account\AccountId;
use PhpFinance\DoubleEntry\Domain\Account\AccountManager;
use PhpFinance\DoubleEntry\Domain\Account\AccountRepositoryInterface;
use PhpFinance\DoubleEntry\Domain\Posting\Factory\EntryData;
use PhpFinance\DoubleEntry\Domain\Posting\Factory\PostingFactory;
use PhpFinance\DoubleEntry\Domain\Posting\Posting;
use PhpFinance\DoubleEntry\Domain\Posting\PostingRepositoryInterface;

trait TestTrait
{
    protected function assertSameMoneyBags(MoneyBag $expectedMoneyBag, MoneyBag $moneyBag): void
    {
        $prepareFn = static fn(BigRational $value) => (string) $value->simplified();
        $expectedAmounts = array_map($prepareFn, $expectedMoneyBag->getAmounts());
        $amounts = array_map($prepareFn, $moneyBag->getAmounts());
        static::assertEquals($expectedAmounts, $amounts);
    }

    protected static function createAccount(
        ?string $id = null,
        ?string $chartId = null,
        ?Account $parent = null,
        ?string $name = null,
    ): Account {
        return new Account(
            $id === null ? self::createAccountIdFactory()->create() : new AccountId($id),
            $chartId === null ? null : new AccountChartId($chartId),
            $parent,
            $name,
        );
    }

    protected static function createAccountIdFactory(array $ids = []): TestAccountIdFactory
    {
        $factory = new TestAccountIdFactory();

        if (!empty($ids)) {
            $factory->setStrings(...$ids);
        }

        return $factory;
    }

    protected static function createAccountRepository(Account ...$accounts): InMemoryAccountRepository
    {
        return new InMemoryAccountRepository($accounts);
    }

    /**
     * @param string[] $accountIds
     */
    protected static function createAccountManager(
        array $accountIds = [],
        ?AccountRepositoryInterface $accountRepository = null,
        ?PostingRepositoryInterface $postingRepository = null,
    ): AccountManager {
        return new AccountManager(
            $accountRepository ?? new InMemoryAccountRepository(),
            self::createAccountIdFactory($accountIds),
            $postingRepository ?? new InMemoryPostingRepository(),
        );
    }

    protected static function createPosting(
        EntryData $debitData,
        EntryData $creditData,
        ?string $id = null,
        ?DateTimeImmutable $date = null,
        ?Money $amount = null,
    ): Posting {
        $factory = new PostingFactory(
            self::createPostingIdFactory($id === null ? [] : [$id])
        );

        return $factory->create(
            $date ?? new DateTimeImmutable(),
            $amount ?? Money::of(7, 'USD'),
            $debitData,
            $creditData,
        );
    }

    protected static function createPostingIdFactory(array $ids = []): TestPostingIdFactory
    {
        $factory = new TestPostingIdFactory();

        if (!empty($ids)) {
            $factory->setStrings(...$ids);
        }

        return $factory;
    }

    protected static function createPostingRepository(Posting ...$postings): InMemoryPostingRepository
    {
        return new InMemoryPostingRepository($postings);
    }
}
