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

    public function existsChildren(Account $account): bool;

    public function save(Account $account): void;

    public function delete(Account $account): void;
}
