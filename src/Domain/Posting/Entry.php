<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Posting;

use Brick\Money\Money;
use DateTimeImmutable;
use PhpFinance\DoubleEntry\Domain\Account\AccountId;

use function array_key_exists;

final readonly class Entry
{
    public function __construct(
        public DateTimeImmutable $date,
        public Money $amount,
        public AccountId $accountId,
        private array $dimensions = [],
    ) {
    }

    /**
     * @throws DimensionNotFound
     */
    public function getDimension(int|string $name): mixed
    {
        if ($this->hasDimension($name)) {
            return $this->dimensions[$name];
        }

        throw new DimensionNotFound($name);
    }

    public function hasDimension(int|string $name): bool
    {
        return array_key_exists($name, $this->dimensions);
    }
}
