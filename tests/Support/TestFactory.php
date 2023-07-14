<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Support;

use Brick\Money\Money;
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

final class TestFactory
{
    public static function createAccount(
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

    public static function createAccountIdFactory(array $ids = []): TestAccountIdFactory
    {
        $factory = new TestAccountIdFactory();

        if (!empty($ids)) {
            $factory->setStrings(...$ids);
        }

        return $factory;
    }

    public static function createAccountRepository(Account ...$accounts): InMemoryAccountRepository
    {
        return new InMemoryAccountRepository($accounts);
    }

    public static function createPosting(
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

    public static function createPostingIdFactory(array $ids = []): TestPostingIdFactory
    {
        $factory = new TestPostingIdFactory();

        if (!empty($ids)) {
            $factory->setStrings(...$ids);
        }

        return $factory;
    }

    public static function createPostingRepository(Posting ...$postings): InMemoryPostingRepository
    {
        return new InMemoryPostingRepository($postings);
    }
}
