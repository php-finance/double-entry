<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Posting;

use Brick\Money\Money;
use DateTimeImmutable;
use PhpFinance\DoubleEntry\Domain\Account\AccountId;
use PhpFinance\DoubleEntry\Domain\Posting\Exception\DimensionNotFoundException;
use PhpFinance\DoubleEntry\Domain\Posting\Factory\PostingFactory;

use function array_key_exists;

final readonly class Entry
{
    /**
     * Don't run directly, use {@see PostingFactory} instead of.
     */
    public function __construct(
        public DateTimeImmutable $date,
        public Money $amount,
        public AccountId $accountId,
        private array $dimensions = [],
    ) {
    }

    /**
     * @throws DimensionNotFoundException
     */
    public function getDimension(int|string $name): mixed
    {
        if ($this->hasDimension($name)) {
            return $this->dimensions[$name];
        }

        throw new DimensionNotFoundException($name);
    }

    public function hasDimension(int|string $name): bool
    {
        return array_key_exists($name, $this->dimensions);
    }
}
