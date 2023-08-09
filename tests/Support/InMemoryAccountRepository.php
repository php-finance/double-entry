<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Support;

use PhpFinance\DoubleEntry\Domain\Account\Account;
use PhpFinance\DoubleEntry\Domain\Account\AccountFilter;
use PhpFinance\DoubleEntry\Domain\Account\AccountId;
use PhpFinance\DoubleEntry\Domain\Account\AccountRepositoryInterface;
use PhpFinance\DoubleEntry\Domain\Account\Exception\AccountNotFoundException;

use function array_key_exists;

final class InMemoryAccountRepository implements AccountRepositoryInterface
{
    /**
     * @param Account[] $accounts
     */
    private array $accounts;

    /**
     * @param Account[] $accounts
     */
    public function __construct(array $accounts = [])
    {
        foreach ($accounts as $account) {
            $this->accounts[$account->id->value] = $account;
        }
    }

    public function get(AccountId $id): Account
    {
        return $this->accounts[$id->value] ?? throw new AccountNotFoundException();
    }

    public function find(AccountFilter $filter): array
    {
        return array_values(
            array_filter(
                $this->accounts,
                static function (Account $account) use ($filter): bool {
                    if ($filter->getAccountChartId() !== null && !$account->chartId->isEqualTo($filter->getAccountChartId())) {
                        return false;
                    }
                    return true;
                }
            )
        );
    }

    public function exists(AccountId $id): bool
    {
        return array_key_exists($id->value, $this->accounts);
    }

    public function hasChildren(Account $account): bool
    {
        foreach ($this->accounts as $inMemoryAccount) {
            if ($inMemoryAccount->getParentId()?->isEqualTo($account->id) === true) {
                return true;
            }
        }

        return false;
    }

    public function save(Account $account): void
    {
        $this->accounts[$account->id->value] = $account;
    }

    public function delete(Account $account): void
    {
        unset($this->accounts[$account->id->value]);
    }

    /**
     * @return Account[]
     */
    public function getAll(): array
    {
        return array_values($this->accounts);
    }
}
