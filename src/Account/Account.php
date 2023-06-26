<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Account;

use InvalidArgumentException;

final class Account
{
    private const NAME_CHARS_LIMIT = 50;

    private string $name;

    public function __construct(
        public readonly AccountId $id,
        public readonly AccountChartId $chartId,
        ?string $name = null,
    ) {
        $this->setName($name ?? $id->value);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function rename(string $name): void
    {
        $this->setName($name);
    }

    private function setName(string $name): void
    {
        $length = mb_strlen($name);
        if ($length === 0 || $length > self::NAME_CHARS_LIMIT) {
            throw new InvalidArgumentException(
                sprintf(
                    'Account name must be non-empty and no greater than %d symbols.',
                    self::NAME_CHARS_LIMIT
                )
            );
        }

        $this->name = $name;
    }
}