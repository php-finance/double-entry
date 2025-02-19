<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Posting\Exception;

use LogicException;

final class DimensionNotFoundException extends LogicException
{
    public function __construct(int|string $name)
    {
        parent::__construct(
            sprintf(
                'Entry does not have dimension "%s".',
                $name,
            ),
        );
    }
}
