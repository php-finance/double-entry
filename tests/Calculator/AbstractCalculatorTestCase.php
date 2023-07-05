<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Calculator;

use Brick\Money\Currency;
use Brick\Money\Money;
use Brick\Money\MoneyBag;
use DateTimeImmutable;
use PhpFinance\DoubleEntry\Domain\Account\Account;
use PhpFinance\DoubleEntry\Domain\Calculator\CalculatorInterface;
use PhpFinance\DoubleEntry\Domain\Calculator\Filter;
use PhpFinance\DoubleEntry\Domain\Posting\Factory\EntryData;
use PhpFinance\DoubleEntry\Domain\Posting\Posting;
use PhpFinance\DoubleEntry\Tests\Support\TestTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

abstract class AbstractCalculatorTestCase extends TestCase
{
    use TestTrait;

    public static function dataCalc(): array
    {
        $data = [];

        /**
         * Calculate without filter
         *
         * Dr wallet Cr incomes 100 USD
         * Dr wallet Cr incomes 150 USD
         * Dr expenses Cr wallet 30 USD
         * Dr wallet Cr incomes 25 USD
         */
        $incomesAccount = self::createAccount('incomes');
        $expensesAccount = self::createAccount('expenses');
        $walletAccount = self::createAccount('wallet');
        $data['without-filter'] = [
            (new MoneyBag())->add(Money::of(275, 'USD')), // 100 + 150 + 25
            (new MoneyBag())->add(Money::of(30, 'USD')), // 30
            (new MoneyBag())->add(Money::of(245, 'USD')), // 100 + 150 - 30 + 25
            [$incomesAccount, $expensesAccount, $walletAccount],
            [
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    amount: Money::of(100, 'USD'),
                ),
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    amount: Money::of(150, 'USD'),
                ),
                self::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount),
                    amount: Money::of(30, 'USD'),
                ),
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    amount: Money::of(25, 'USD'),
                ),
            ],
            $walletAccount,
        ];

        /**
         * Calculate with filter by one dimension
         *
         * Dr wallet (John) Cr incomes 100 USD
         * Dr wallet (Mike) Cr incomes 150 USD
         * Dr expenses Cr wallet (John) 30 USD
         */
        $incomesAccount = self::createAccount('incomes');
        $expensesAccount = self::createAccount('expenses');
        $walletAccount = self::createAccount('wallet');
        $data['filter-by-one-dimension'] = [
            (new MoneyBag())->add(Money::of(100, 'USD')),
            (new MoneyBag())->add(Money::of(30, 'USD')),
            (new MoneyBag())->add(Money::of(70, 'USD')),
            [$incomesAccount, $expensesAccount, $walletAccount],
            [
                self::createPosting(
                    new EntryData($walletAccount, ['contractor' => 'John']),
                    new EntryData($incomesAccount),
                    amount: Money::of(100, 'USD'),
                ),
                self::createPosting(
                    new EntryData($walletAccount, ['contractor' => 'Mike']),
                    new EntryData($incomesAccount),
                    amount: Money::of(150, 'USD'),
                ),
                self::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount, ['contractor' => 'John']),
                    amount: Money::of(30, 'USD'),
                ),
            ],
            $walletAccount,
            new Filter(dimensions: ['contractor' => 'John']),
        ];

        /**
         * Calculate with filter by two dimension
         *
         * Dr wallet (John, High) Cr incomes 100 USD
         * Dr wallet (John, Normal) Cr incomes 90 USD
         * Dr wallet (John, High) Cr incomes 5 USD
         * Dr expenses Cr wallet (John, High) 30 USD
         * Dr wallet (Mike, High) Cr incomes 150 USD
         * Dr expenses Cr wallet (John, Normal) 10 USD
         */
        $incomesAccount = self::createAccount('incomes');
        $expensesAccount = self::createAccount('expenses');
        $walletAccount = self::createAccount('wallet');
        $data['filter-by-two-dimensions'] = [
            (new MoneyBag())->add(Money::of(105, 'USD')), // 100 + 5
            (new MoneyBag())->add(Money::of(30, 'USD')),
            (new MoneyBag())->add(Money::of(75, 'USD')),
            [$incomesAccount, $expensesAccount, $walletAccount],
            [
                self::createPosting(
                    new EntryData($walletAccount, ['contractor' => 'John', 'priority' => 'High']),
                    new EntryData($incomesAccount),
                    amount: Money::of(100, 'USD'),
                ),
                self::createPosting(
                    new EntryData($walletAccount, ['contractor' => 'John', 'priority' => 'Normal']),
                    new EntryData($incomesAccount),
                    amount: Money::of(90, 'USD'),
                ),
                self::createPosting(
                    new EntryData($walletAccount, ['contractor' => 'John', 'priority' => 'High']),
                    new EntryData($incomesAccount),
                    amount: Money::of(5, 'USD'),
                ),
                self::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount, ['contractor' => 'John', 'priority' => 'High']),
                    amount: Money::of(30, 'USD'),
                ),
                self::createPosting(
                    new EntryData($walletAccount, ['contractor' => 'Mike', 'priority' => 'High']),
                    new EntryData($incomesAccount),
                    amount: Money::of(150, 'USD'),
                ),
                self::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount, ['contractor' => 'John', 'priority' => 'Normal']),
                    amount: Money::of(10, 'USD'),
                ),
            ],
            $walletAccount,
            new Filter(dimensions: ['contractor' => 'John', 'priority' => 'High']),
        ];

        /**
         * Calculate with filter by date from
         *
         * 01.12.2020 Dr wallet Cr incomes 100 USD
         * 15.12.2020 Dr wallet Cr incomes 90 USD
         * 25.01.2021 Dr wallet Cr incomes 5 USD
         * 20.02.2021 Dr expenses Cr wallet 30 USD
         * 21.02.2021 Dr wallet Cr incomes 150 USD
         * 05.03.2021 Dr expenses Cr wallet 10 USD
         */
        $incomesAccount = self::createAccount('incomes');
        $expensesAccount = self::createAccount('expenses');
        $walletAccount = self::createAccount('wallet');
        $data['filter-by-date-from'] = [
            (new MoneyBag())->add(Money::of(155, 'USD')), // 5 + 150
            (new MoneyBag())->add(Money::of(40, 'USD')), // 30 + 10
            (new MoneyBag())->add(Money::of(115, 'USD')),
            [$incomesAccount, $expensesAccount, $walletAccount],
            [
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    date: new DateTimeImmutable('01.12.2020'),
                    amount: Money::of(100, 'USD'),
                ),
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    date: new DateTimeImmutable('15.12.2020'),
                    amount: Money::of(90, 'USD'),
                ),
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    date: new DateTimeImmutable('25.01.2021'),
                    amount: Money::of(5, 'USD'),
                ),
                self::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount),
                    date: new DateTimeImmutable('20.02.2021'),
                    amount: Money::of(30, 'USD'),
                ),
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    date: new DateTimeImmutable('21.02.2021'),
                    amount: Money::of(150, 'USD'),
                ),
                self::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount),
                    date: new DateTimeImmutable('05.03.2021'),
                    amount: Money::of(10, 'USD'),
                ),
            ],
            $walletAccount,
            new Filter(dateFrom: new DateTimeImmutable('01.01.2021')),
        ];

        /**
         * Calculate with filter by date to
         *
         * 01.12.2020 Dr wallet Cr incomes 100 USD
         * 15.12.2020 Dr wallet Cr incomes 90 USD
         * 25.01.2021 Dr wallet Cr incomes 5 USD
         * 19.02.2021 Dr expenses Cr wallet 30 USD
         * 21.02.2021 Dr wallet Cr incomes 150 USD
         * 05.03.2021 Dr expenses Cr wallet 10 USD
         */
        $incomesAccount = self::createAccount('incomes');
        $expensesAccount = self::createAccount('expenses');
        $walletAccount = self::createAccount('wallet');
        $data['filter-by-date-to'] = [
            (new MoneyBag())->add(Money::of(195, 'USD')), // 100 + 90 + 5
            (new MoneyBag())->add(Money::of(30, 'USD')),
            (new MoneyBag())->add(Money::of(165, 'USD')),
            [$incomesAccount, $expensesAccount, $walletAccount],
            [
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    date: new DateTimeImmutable('01.12.2020'),
                    amount: Money::of(100, 'USD'),
                ),
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    date: new DateTimeImmutable('15.12.2020'),
                    amount: Money::of(90, 'USD'),
                ),
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    date: new DateTimeImmutable('25.01.2021'),
                    amount: Money::of(5, 'USD'),
                ),
                self::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount),
                    date: new DateTimeImmutable('19.02.2021'),
                    amount: Money::of(30, 'USD'),
                ),
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    date: new DateTimeImmutable('21.02.2021'),
                    amount: Money::of(150, 'USD'),
                ),
                self::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount),
                    date: new DateTimeImmutable('05.03.2021'),
                    amount: Money::of(10, 'USD'),
                ),
            ],
            $walletAccount,
            new Filter(dateTo: new DateTimeImmutable('20.02.2021')),
        ];

        /**
         * Calculate with filter by date period
         *
         * 01.12.2020 Dr wallet Cr incomes 100 USD
         * 15.12.2020 Dr wallet Cr incomes 90 USD
         * 25.01.2021 Dr wallet Cr incomes 5 USD
         * 20.02.2021 Dr expenses Cr wallet 30 USD
         * 21.02.2021 Dr wallet Cr incomes 150 USD
         * 05.03.2021 Dr expenses Cr wallet 10 USD
         */
        $incomesAccount = self::createAccount('incomes');
        $expensesAccount = self::createAccount('expenses');
        $walletAccount = self::createAccount('wallet');
        $data['filter-by-date-period'] = [
            (new MoneyBag())->add(Money::of(155, 'USD')), // 5 + 150
            (new MoneyBag())->add(Money::of(30, 'USD')),
            (new MoneyBag())->add(Money::of(125, 'USD')),
            [$incomesAccount, $expensesAccount, $walletAccount],
            [
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    date: new DateTimeImmutable('01.12.2020'),
                    amount: Money::of(100, 'USD'),
                ),
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    date: new DateTimeImmutable('15.12.2020'),
                    amount: Money::of(90, 'USD'),
                ),
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    date: new DateTimeImmutable('25.01.2021'),
                    amount: Money::of(5, 'USD'),
                ),
                self::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount),
                    date: new DateTimeImmutable('20.02.2021'),
                    amount: Money::of(30, 'USD'),
                ),
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    date: new DateTimeImmutable('21.02.2021'),
                    amount: Money::of(150, 'USD'),
                ),
                self::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount),
                    date: new DateTimeImmutable('05.03.2021'),
                    amount: Money::of(10, 'USD'),
                ),
            ],
            $walletAccount,
            new Filter(dateFrom: new DateTimeImmutable('01.01.2021'), dateTo: new DateTimeImmutable('27.02.2021')),
        ];

        /**
         * Calculate with filter by one currency
         *
         * Dr wallet Cr incomes 100 RUB
         * Dr wallet Cr incomes 150 USD
         * Dr wallet Cr incomes 30 USD
         * Dr expenses Cr wallet 71 RUB
         * Dr wallet Cr incomes 22 RUB
         * Dr wallet Cr incomes 500 IDR
         */
        $incomesAccount = self::createAccount('incomes');
        $expensesAccount = self::createAccount('expenses');
        $walletAccount = self::createAccount('wallet');
        $data['filter-by-one-currency'] = [
            (new MoneyBag())->add(Money::of(122, 'RUB')), // 100 + 22
            (new MoneyBag())->add(Money::of(71, 'RUB')), // 30
            (new MoneyBag())->add(Money::of(51, 'RUB')),
            [$incomesAccount, $expensesAccount, $walletAccount],
            [
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    amount: Money::of(100, 'RUB'),
                ),
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    amount: Money::of(150, 'USD'),
                ),
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    amount: Money::of(30, 'USD'),
                ),
                self::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount),
                    amount: Money::of(71, 'RUB'),
                ),
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    amount: Money::of(22, 'RUB'),
                ),
                self::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    amount: Money::of(500, 'IDR'),
                ),
            ],
            $walletAccount,
            new Filter(currencies: [Currency::of('RUB')])
        ];

        return $data;
    }

    #[DataProvider('dataCalc')]
    public function testCalc(
        MoneyBag $expectedDebit,
        MoneyBag $expectedCredit,
        MoneyBag $expectedBalance,
        array $accounts,
        array $postings,
        Account $account,
        ?Filter $filter = null
    ): void {
        $calculator = $this->createCalculator($accounts, $postings);

        $this->assertSameMoneyBags($expectedDebit, $calculator->debit($account, $filter));
        $this->assertSameMoneyBags($expectedCredit, $calculator->credit($account, $filter));
        $this->assertSameMoneyBags($expectedBalance, $calculator->balance($account, $filter));
    }

    /**
     * @param Account[] $accounts
     * @param Posting[] $postings
     */
    abstract protected function createCalculator(array $accounts, array $postings): CalculatorInterface;
}
