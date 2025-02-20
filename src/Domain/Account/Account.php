<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Account;

use InvalidArgumentException;

final class Account
{
    /**
     * @psalm-var non-empty-string|null
     */
    private ?string $name;

    private ?AccountId $parentId;

    /**
     * @psalm-param non-empty-string|null $name
     */
    public function __construct(
        public readonly AccountId $id,
        public readonly ?AccountChartId $chartId = null,
        ?Account $parent = null,
        ?string $name = null,
    ) {
        $this->setName($name);
        $this->setParent($parent);
    }

    /**
     * @psalm-return non-empty-string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function getParentId(): ?AccountId
    {
        return $this->parentId;
    }

    /**
     * @psalm-param non-empty-string|null $name
     */
    public function rename(?string $name): void
    {
        $this->setName($name);
    }

    private function setName(?string $name): void
    {
        if ($name === '') {
            throw new InvalidArgumentException('Account name must be null or non-empty string.');
        }

        $this->name = $name;
    }

    private function setParent(?Account $parent): void
    {
        if ($parent !== null && $this->chartId?->value !== $parent->chartId?->value) {
            throw new InvalidArgumentException('Account chart of parent account is not equal to current.');
        }

        $this->parentId = $parent?->id;
    }
}
