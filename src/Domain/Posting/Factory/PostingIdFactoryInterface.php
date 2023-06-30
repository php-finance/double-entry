<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Posting\Factory;

use PhpFinance\DoubleEntry\Domain\Posting\PostingId;

interface PostingIdFactoryInterface
{
    public function create(): PostingId;
}
