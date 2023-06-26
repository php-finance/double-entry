<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Account;

use InvalidArgumentException;

final class Account
{
    private const NAME_CHARS_LIMIT = 50;

    /**
     * @psalm-var non-empty-string
     */
    private string $name;

    public function __construct(
        public readonly AccountId $id,
        public readonly ?AccountId $parentId,
        public readonly AccountChartId $chartId,
        ?string $name = null,
    ) {
        $this->setName($name ?? $id->value);
    }

    /**
     * @psalm-return non-empty-string
     */
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
        /** @psalm-var non-empty-string $name */

        $this->name = $name;
    }
}