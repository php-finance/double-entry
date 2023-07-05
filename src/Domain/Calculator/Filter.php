<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Calculator;

use Brick\Money\Currency;
use DateTimeImmutable;

final class Filter
{
    /**
     * @param array $dimensions Entry must match to all specified dimensions.
     * @param Currency[] $currencies Money must be in one of specified currencies.
     */
    public function __construct(
        private array $dimensions = [],
        private ?DateTimeImmutable $dateFrom = null,
        private ?DateTimeImmutable $dateTo = null,
        private array $currencies = [],
    ) {
    }

    public function getDimensions(): array
    {
        return $this->dimensions;
    }

    public function getDateFrom(): ?DateTimeImmutable
    {
        return $this->dateFrom;
    }

    public function getDateTo(): ?DateTimeImmutable
    {
        return $this->dateTo;
    }

    /**
     * @return Currency[]
     */
    public function getCurrencies(): array
    {
        return $this->currencies;
    }

    public function withDimensions(array $dimensions): self
    {
        $new = clone $this;
        $new->dimensions = $dimensions;
        return $new;
    }

    public function withDateFrom(?DateTimeImmutable $date): self
    {
        $new = clone $this;
        $new->dateFrom = $date;
        return $new;
    }

    public function withDateTo(?DateTimeImmutable $date): self
    {
        $new = clone $this;
        $new->dateTo = $date;
        return $new;
    }

    /**
     * @param Currency[] $currencies
     */
    public function withCurrencies(array $currencies): self
    {
        $new = clone $this;
        $new->currencies = $currencies;
        return $new;
    }
}
