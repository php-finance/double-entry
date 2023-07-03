<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Account;

use PhpFinance\DoubleEntry\Domain\Account\Exception\AccountNotFoundException;

/**
 * Use {@see AccountManager} instead of repository direct usage.
 */
interface AccountRepositoryInterface
{
    /**
     * @throws AccountNotFoundException
     */
    public function get(AccountId $id): Account;

    public function hasChildren(Account $account): bool;

    public function save(Account $account): void;

    public function delete(Account $account): void;
}
