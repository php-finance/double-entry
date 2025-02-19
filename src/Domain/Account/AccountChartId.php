<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Account;

final readonly class AccountChartId
{
    public function __construct(
        public string $value,
    ) {}

    public function isEqualTo(AccountChartId $id): bool
    {
        return $this->value === $id->value;
    }
}
