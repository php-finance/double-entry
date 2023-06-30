<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Calculator;

use DateTimeImmutable;

final class Filter
{
    public function __construct(
        private array $dimensions = [],
        private ?DateTimeImmutable $dateFrom = null,
        private ?DateTimeImmutable $dateTo = null,
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
}
