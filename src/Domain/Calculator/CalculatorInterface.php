<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Calculator;

use Brick\Money\MoneyBag;
use PhpFinance\DoubleEntry\Domain\Account\Account;

interface CalculatorInterface
{
    public function debit(Account $account, ?Filter $filter = null): MoneyBag;

    public function credit(Account $account, ?Filter $filter = null): MoneyBag;

    public function balance(Account $account, ?Filter $filter = null): MoneyBag;
}
