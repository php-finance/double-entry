<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Account;

interface AccountIdFactoryInterface
{
    public function create(): AccountId;
}
