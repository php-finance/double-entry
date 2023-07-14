<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Support;

use PhpFinance\DoubleEntry\Domain\Transaction\Factory\TransactionIdFactoryInterface;
use PhpFinance\DoubleEntry\Domain\Transaction\TransactionId;

final class TestTransactionIdFactory extends BaseIdFactory implements TransactionIdFactoryInterface
{
    public function create(): TransactionId
    {
        return new TransactionId($this->getNextString());
    }
}
