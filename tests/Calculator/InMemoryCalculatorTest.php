<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Calculator;

use PhpFinance\DoubleEntry\Domain\Calculator\CalculatorInterface;
use PhpFinance\DoubleEntry\Tests\Support\InMemoryCalculator;

final class InMemoryCalculatorTest extends AbstractCalculatorTestCase
{
    protected function createCalculator(array $accounts, array $postings): CalculatorInterface
    {
        return new InMemoryCalculator(
            self::createPostingRepository(...$postings)
        );
    }
}
