<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Account;

use PhpFinance\DoubleEntry\Domain\Account\Exception\AccountNotFoundException;

interface AccountRepositoryInterface
{
    /**
     * @throws AccountNotFoundException
     */
    public function get(AccountId $id): Account;

    public function hasChildren(Account $account): bool;

    public function save(Account $account): void;

    /**
     * Don't run directly, use {@see AccountManager::delete()} instead of.
     */
    public function delete(Account $account): void;
}
