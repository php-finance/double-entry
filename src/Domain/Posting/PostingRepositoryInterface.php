<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Posting;

use PhpFinance\DoubleEntry\Domain\Account\Account;
use PhpFinance\DoubleEntry\Domain\Posting\Exception\PostingNotFoundException;

interface PostingRepositoryInterface
{
    /**
     * @throws PostingNotFoundException
     */
    public function get(PostingId $id): Posting;

    public function exists(PostingId $id): bool;

    public function existsWithAccount(Account $account): bool;

    public function save(Posting $posting): void;
}
