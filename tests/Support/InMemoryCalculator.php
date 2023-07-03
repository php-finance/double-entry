<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Support;

use Brick\Money\MoneyBag;
use PhpFinance\DoubleEntry\Domain\Account\Account;
use PhpFinance\DoubleEntry\Domain\Calculator\CalculatorInterface;
use PhpFinance\DoubleEntry\Domain\Calculator\Filter;
use PhpFinance\DoubleEntry\Domain\Posting\Entry;

final readonly class InMemoryCalculator implements CalculatorInterface
{
    public function __construct(
        private InMemoryPostingRepository $postingRepository,
    ) {
    }

    public function calcDebit(Account $account, ?Filter $filter = null): MoneyBag
    {
        $moneyBag = new MoneyBag();
        foreach ($this->postingRepository->getAll() as $posting) {
            if ($this->isPostingFitsAccountAndFilter($posting->debit, $account, $filter)) {
                $moneyBag->add($posting->debit->amount);
            }
        }
        return $moneyBag;
    }

    public function calcCredit(Account $account, ?Filter $filter = null): MoneyBag
    {
        $moneyBag = new MoneyBag();
        foreach ($this->postingRepository->getAll() as $posting) {
            if ($this->isPostingFitsAccountAndFilter($posting->credit, $account, $filter)) {
                $moneyBag->add($posting->credit->amount);
            }
        }
        return $moneyBag;
    }

    public function calcBalance(Account $account, ?Filter $filter = null): MoneyBag
    {
        $moneyBag = new MoneyBag();
        foreach ($this->postingRepository->getAll() as $posting) {
            if ($this->isPostingFitsAccountAndFilter($posting->debit, $account, $filter)) {
                $moneyBag->add($posting->debit->amount);
            }
            if ($this->isPostingFitsAccountAndFilter($posting->credit, $account, $filter)) {
                $moneyBag->subtract($posting->credit->amount);
            }
        }
        return $moneyBag;
    }

    private function isPostingFitsAccountAndFilter(Entry $entry, Account $account, ?Filter $filter): bool
    {
        if (!$entry->accountId->isEqualTo($account->id)) {
            return false;
        }

        if ($filter === null) {
            return true;
        }

        foreach ($filter->getDimensions() as $name => $value) {
            if (!$entry->hasDimension($name) || $entry->getDimension($name) !== $value) {
                return false;
            }
        }

        $dateFrom = $filter->getDateFrom();
        if ($dateFrom !== null && $entry->date < $dateFrom) {
            return false;
        }

        $dateTo = $filter->getDateTo();
        if ($dateTo !== null && $entry->date > $dateTo) {
            return false;
        }

        $success = false;
        $entryCurrency = $entry->amount->getCurrency();
        foreach ($filter->getCurrencies() as $currency) {
            if ($currency->is($entryCurrency)) {
                $success = true;
                break;
            }
        }
        if (!$success) {
            return false;
        }

        return true;
    }
}
