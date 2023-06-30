<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Calculator;

use Brick\Money\Money;
use PhpFinance\DoubleEntry\Domain\Account\Account;

interface CalculatorInterface
{
    public function calcDebit(Account $account, ?Filter $filter = null): Money;

    public function calcCredit(Account $account, ?Filter $filter = null): Money;

    public function calcBalance(Account $account, ?Filter $filter = null): Money;
}
