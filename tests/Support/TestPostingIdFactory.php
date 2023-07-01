<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Support;

use PhpFinance\DoubleEntry\Domain\Posting\Factory\PostingIdFactoryInterface;
use PhpFinance\DoubleEntry\Domain\Posting\PostingId;

final class TestPostingIdFactory extends BaseIdFactory implements PostingIdFactoryInterface
{
    public function create(): PostingId
    {
        return new PostingId($this->getNextString());
    }
}
