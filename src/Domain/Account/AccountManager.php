<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Account;

use PhpFinance\DoubleEntry\Domain\Account\Exception\AccountDeletionNotPossibleException;
use PhpFinance\DoubleEntry\Domain\Posting\PostingRepositoryInterface;

final readonly class AccountManager
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private AccountIdFactoryInterface $accountIdFactory,
        private PostingRepositoryInterface $postingRepository,
    ) {}

    public function get(AccountId $id): Account
    {
        return $this->accountRepository->get($id);
    }

    /**
     * @return Account[]
     * @psalm-return list<Account>
     */
    public function find(?AccountFilter $filter = null): array
    {
        return $this->accountRepository->find($filter ?? new AccountFilter());
    }

    public function exists(AccountId $id): bool
    {
        return $this->accountRepository->exists($id);
    }

    public function save(Account $account): void
    {
        $this->accountRepository->save($account);
    }

    /**
     * @psalm-param non-empty-string|null $name
     */
    public function create(?AccountChartId $chartId = null, ?Account $parent = null, ?string $name = null): Account
    {
        $account = new Account(
            $this->accountIdFactory->create(),
            $chartId,
            $parent,
            $name,
        );

        $this->accountRepository->save($account);

        return $account;
    }

    /**
     * @throws AccountDeletionNotPossibleException
     */
    public function delete(Account $account): void
    {
        if ($this->accountRepository->hasChildren($account)) {
            $name = $account->getName();
            throw new AccountDeletionNotPossibleException(
                'Deletion isn\'t possible, account' . ($name !== null ? ' "' . $name . '"' : '') . ' has children.',
            );
        }

        if ($this->postingRepository->existsWithAccount($account)) {
            $name = $account->getName();
            throw new AccountDeletionNotPossibleException(
                'Deletion isn\'t possible, entries with account' . ($name !== null ? ' "' . $name . '"' : '') . ' exists.',
            );
        }

        $this->accountRepository->delete($account);
    }
}
