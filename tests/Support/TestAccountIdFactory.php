<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Support;

use PhpFinance\DoubleEntry\Domain\Account\AccountId;
use PhpFinance\DoubleEntry\Domain\Account\AccountIdFactoryInterface;

final class TestAccountIdFactory extends BaseIdFactory implements AccountIdFactoryInterface
{
    public function create(): AccountId
    {
        return new AccountId($this->getNextString());
    }
}
