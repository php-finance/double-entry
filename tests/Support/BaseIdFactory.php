<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Support;

use PhpFinance\DoubleEntry\Domain\Account\AccountId;
use PhpFinance\DoubleEntry\Domain\Account\AccountIdFactoryInterface;

abstract class BaseIdFactory
{
    /**
     * @var string[]
     */
    private array $strings = [];

    final public function setStrings(string ...$strings): static
    {
        $this->strings = $strings;
        return $this;
    }

    final protected function getNextString(): string
    {
        $value = current($this->strings);
        if ($value === false) {
            return microtime(true) . mt_rand();
        }

        next($this->strings);
        return $value;
    }
}
