<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Posting;

final readonly class PostingId
{
    public function __construct(
        public string $value,
    ) {}

    public function isEqualTo(PostingId $id): bool
    {
        return $this->value === $id->value;
    }
}
