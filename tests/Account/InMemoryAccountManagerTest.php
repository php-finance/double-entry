<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Account;

use PhpFinance\DoubleEntry\Domain\Account\Account;
use PhpFinance\DoubleEntry\Domain\Account\AccountRepositoryInterface;
use PhpFinance\DoubleEntry\Domain\Posting\Posting;
use PhpFinance\DoubleEntry\Domain\Posting\PostingRepositoryInterface;
use PhpFinance\DoubleEntry\Tests\Support\InMemoryAccountRepository;
use PhpFinance\DoubleEntry\Tests\Support\InMemoryPostingRepository;

final class InMemoryAccountManagerTest extends AbstractAccountManagerTestCase
{
    protected function createAccountRepository(Account ...$accounts): AccountRepositoryInterface
    {
        return new InMemoryAccountRepository($accounts);
    }

    protected function createPostingRepository(Posting ...$postings): PostingRepositoryInterface
    {
        return new InMemoryPostingRepository($postings);
    }
}
