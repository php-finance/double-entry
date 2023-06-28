<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Operation;

use PhpFinance\DoubleEntry\Domain\Posting\Posting;

final readonly class Operation
{
    /**
     * @param Posting[] $transactions
     * @psalm-param non-empty-list<Posting> $transactions
     */
    public function __construct(
        public OperationId $id,
        public array $transactions,
    ) {
    }
}
