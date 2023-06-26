<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Operation;

final readonly class OperationId
{
    public function __construct(
        public string $value
    ) {
    }
}
