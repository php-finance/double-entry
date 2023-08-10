<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Account;

final class AccountFilter
{
    private ?AccountChartId $accountChartId = null;

    public function getAccountChartId(): ?AccountChartId
    {
        return $this->accountChartId;
    }

    public function withAccountChartId(?AccountChartId $accountChartId): self
    {
        $new = clone $this;
        $new->accountChartId = $accountChartId;
        return $new;
    }
}
