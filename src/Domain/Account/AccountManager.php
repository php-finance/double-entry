<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Account;

use PhpFinance\DoubleEntry\Domain\Account\Exception\AccountDeletionNotPossibleException;
use PhpFinance\DoubleEntry\Domain\Posting\PostingRepositoryInterface;

final class AccountManager
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
        private readonly PostingRepositoryInterface $postingRepository,
    ) {
    }

    /**
     * @throws AccountDeletionNotPossibleException
     */
    public function delete(Account $account): void
    {
        if ($this->accountRepository->hasChildren($account)) {
            $name = $account->getName();
            throw new AccountDeletionNotPossibleException(
                'Deletion not possible, account' . ($name !== null ? ' "' . $name . '"' : '') . ' has children.',
            );
        }

        if ($this->postingRepository->existsWithAccount($account)) {
            $name = $account->getName();
            throw new AccountDeletionNotPossibleException(
                'Deletion not possible, entries with account' . ($name !== null ? ' "' . $name . '"' : '') . ' exists.',
            );
        }

        $this->accountRepository->delete($account);
    }
}
