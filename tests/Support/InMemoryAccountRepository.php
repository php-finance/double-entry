<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Support;

use PhpFinance\DoubleEntry\Domain\Account\Account;
use PhpFinance\DoubleEntry\Domain\Account\AccountId;
use PhpFinance\DoubleEntry\Domain\Account\AccountRepositoryInterface;
use PhpFinance\DoubleEntry\Domain\Account\Exception\AccountNotFoundException;

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
