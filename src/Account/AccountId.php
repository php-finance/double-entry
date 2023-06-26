<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Account;

final readonly class AccountId
{
    public function __construct(
        public string $value
    ) {
    }
}
