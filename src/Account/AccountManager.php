<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Account;

use PhpFinance\DoubleEntry\Account\Exception\AccountDeletionNotPossibleException;

final class AccountManager
{
    public function __construct(
        private readonly AccountRepositoryInterface $repository,
    ) {
    }

    /**
     * @throws AccountDeletionNotPossibleException
     */
    public function delete(Account $account): void
    {
        if ($this->repository->existsChildren($account)) {
            throw new AccountDeletionNotPossibleException($account);
        }

        $this->repository->delete($account);
    }
}
