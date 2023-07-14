<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Transaction\Factory;

use PhpFinance\DoubleEntry\Domain\Transaction\TransactionId;

interface TransactionIdFactoryInterface
{
    public function create(): TransactionId;
}
