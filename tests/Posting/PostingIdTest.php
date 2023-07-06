<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Posting;

use PhpFinance\DoubleEntry\Domain\Posting\PostingId;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PostingIdTest extends TestCase
{
    public function testBase(): void
    {
        $id = new PostingId('7');

        $this->assertSame('7', $id->value);
    }

    public static function dataIsEqualTo(): array
    {
        return [
            'equal' => [
                true,
                new PostingId('7'),
                new PostingId('7'),
            ],
            'non-equal' => [
                false,
                new PostingId('7'),
                new PostingId('42'),
            ],
        ];
    }

    #[DataProvider('dataIsEqualTo')]
    public function testIsEqualTo(bool $expected, PostingId $id1, PostingId $id2): void
    {
        $this->assertSame($expected, $id1->isEqualTo($id2));
    }
}
