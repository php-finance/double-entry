<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Transaction\Factory;

use PhpFinance\DoubleEntry\Domain\Transaction\TransactionId;

interface TransactionIdFactory
{
    public function create(): TransactionId;
}
