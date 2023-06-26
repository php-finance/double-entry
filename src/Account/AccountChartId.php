<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Account;

final readonly class AccountChartId
{
    public function __construct(
        public string $value
    ) {
    }
}
