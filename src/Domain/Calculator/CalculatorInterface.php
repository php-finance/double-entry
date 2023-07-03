<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Calculator;

use Brick\Money\MoneyBag;
use PhpFinance\DoubleEntry\Domain\Account\Account;

interface CalculatorInterface
{
    public function calcDebit(Account $account, ?Filter $filter = null): MoneyBag;

    public function calcCredit(Account $account, ?Filter $filter = null): MoneyBag;

    public function calcBalance(Account $account, ?Filter $filter = null): MoneyBag;
}
