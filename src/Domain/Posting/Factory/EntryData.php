<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Posting\Factory;

use PhpFinance\DoubleEntry\Domain\Account\Account;

final readonly class EntryData
{
    public function __construct(
        public Account $account,
        public array $dimensions = [],
    ) {
    }
}
