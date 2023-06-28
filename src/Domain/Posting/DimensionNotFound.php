<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Posting;

use LogicException;

final class DimensionNotFound extends LogicException
{
    public function __construct(int|string $name)
    {
        parent::__construct(
            sprintf(
                'Entry do not contain dimension "%s".',
                $name
            )
        );
    }
}
