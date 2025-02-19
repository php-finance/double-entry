<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Account;

final readonly class AccountId
{
    public function __construct(
        public string $value,
    ) {}

    public function isEqualTo(AccountId $id): bool
    {
        return $this->value === $id->value;
    }
}
