<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Support;

use PhpFinance\DoubleEntry\Domain\Account\Account;
use PhpFinance\DoubleEntry\Domain\Posting\Exception\PostingNotFoundException;
use PhpFinance\DoubleEntry\Domain\Posting\Posting;
use PhpFinance\DoubleEntry\Domain\Posting\PostingId;
use PhpFinance\DoubleEntry\Domain\Posting\PostingRepositoryInterface;

use function array_key_exists;

final class InMemoryPostingRepository implements PostingRepositoryInterface
{
    /**
     * @var Posting[]
     */
    private array $postings = [];

    /**
     * @param Posting[] $postings
     */
    public function __construct(array $postings = [])
    {
        foreach ($postings as $posting) {
            $this->postings[$posting->id->value] = $posting;
        }
    }

    public function get(PostingId $id): Posting
    {
        return $this->postings[$id->value] ?? throw new PostingNotFoundException();
    }

    public function exists(PostingId $id): bool
    {
        return array_key_exists($id->value, $this->postings);
    }

    public function existsWithAccount(Account $account): bool
    {
        foreach ($this->postings as $posting) {
            if ($posting->debit->accountId->isEqualTo($account->id)
                || $posting->credit->accountId->isEqualTo($account->id)
            ) {
                return true;
            }
        }

        return false;
    }

    public function save(Posting $posting): void
    {
        $this->postings[$posting->id->value] = $posting;
    }
}
