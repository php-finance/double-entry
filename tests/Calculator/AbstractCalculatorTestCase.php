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
use PhpFinance\DoubleEntry\Tests\Support\TestFactory;
use PhpFinance\DoubleEntry\Tests\Support\TestTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

abstract class AbstractCalculatorTestCase extends TestCase
{
    use TestTrait;

    public static function dataCalc(): array
    {
        $incomesAccount = TestFactory::createAccount('incomes');
        $expensesAccount = TestFactory::createAccount('expenses');
        $walletAccount = TestFactory::createAccount('wallet');

        return [
            'without-filter' => array_merge(
                self::createData1($incomesAccount, $expensesAccount, $walletAccount),
                [
                    (new MoneyBag())->add(Money::of(275, 'USD')), // 100 + 150 + 25
                    (new MoneyBag())->add(Money::of(30, 'USD')), // 30
                    (new MoneyBag())->add(Money::of(245, 'USD')), // 100 + 150 - 30 + 25
                    $walletAccount,
                ]
            ),
            'filter-by-one-currency' => array_merge(
                self::createData2($incomesAccount, $expensesAccount, $walletAccount),
                [
                    (new MoneyBag())->add(Money::of(122, 'RUB')), // 100 + 22
                    (new MoneyBag())->add(Money::of(71, 'RUB')), // 30
                    (new MoneyBag())->add(Money::of(51, 'RUB')),
                    $walletAccount,
                    new Filter(currencies: [Currency::of('RUB')])
                ]
            ),
            'filter-by-two-currency' => array_merge(
                self::createData2($incomesAccount, $expensesAccount, $walletAccount),
                [
                    (new MoneyBag())->add(Money::of(122, 'RUB'))->add(Money::of(180, 'USD')),
                    (new MoneyBag())->add(Money::of(71, 'RUB'))->add(Money::of(22, 'USD')),
                    (new MoneyBag())->add(Money::of(51, 'RUB'))->add(Money::of(158, 'USD')),
                    $walletAccount,
                    new Filter(currencies: [Currency::of('RUB'), Currency::of('USD')])
                ]
            ),
            'filter-by-one-dimension' => array_merge(
                self::createData3($incomesAccount, $expensesAccount, $walletAccount),
                [
                    (new MoneyBag())->add(Money::of(195, 'USD')), // 100 + 90 + 5
                    (new MoneyBag())->add(Money::of(40, 'USD')), // 30 + 10
                    (new MoneyBag())->add(Money::of(155, 'USD')),
                    $walletAccount,
                    new Filter(dimensions: ['contractor' => 'John']),
                ]
            ),
            'filter-by-two-dimensions' => array_merge(
                self::createData3($incomesAccount, $expensesAccount, $walletAccount),
                [
                    (new MoneyBag())->add(Money::of(105, 'USD')), // 100 + 5
                    (new MoneyBag())->add(Money::of(30, 'USD')),
                    (new MoneyBag())->add(Money::of(75, 'USD')),
                    $walletAccount,
                    new Filter(dimensions: ['contractor' => 'John', 'priority' => 'High']),
                ]
            ),
            'filter-by-date-from' => array_merge(
                self::createData4($incomesAccount, $expensesAccount, $walletAccount),
                [
                    (new MoneyBag())->add(Money::of(195, 'USD')), // 100 + 90 + 5
                    (new MoneyBag())->add(Money::of(30, 'USD')),
                    (new MoneyBag())->add(Money::of(165, 'USD')),
                    $walletAccount,
                    new Filter(dateTo: new DateTimeImmutable('20.02.2021')),
                ]
            ),
            'filter-by-date-to' => array_merge(
                self::createData4($incomesAccount, $expensesAccount, $walletAccount),
                [
                    (new MoneyBag())->add(Money::of(155, 'USD')), // 5 + 150
                    (new MoneyBag())->add(Money::of(40, 'USD')), // 30 + 10
                    (new MoneyBag())->add(Money::of(115, 'USD')),
                    $walletAccount,
                    new Filter(dateFrom: new DateTimeImmutable('01.01.2021')),
                ]
            ),
            'filter-by-date-period' => array_merge(
                self::createData4($incomesAccount, $expensesAccount, $walletAccount),
                [
                    (new MoneyBag())->add(Money::of(155, 'USD')), // 5 + 150
                    (new MoneyBag())->add(Money::of(30, 'USD')),
                    (new MoneyBag())->add(Money::of(125, 'USD')),
                    $walletAccount,
                    new Filter(
                        dateFrom: new DateTimeImmutable('01.01.2021'),
                        dateTo: new DateTimeImmutable('27.02.2021')
                    ),
                ]
            ),
        ];
    }

    #[DataProvider('dataCalc')]
    public function testCalc(
        array $accounts,
        array $postings,
        MoneyBag $expectedDebit,
        MoneyBag $expectedCredit,
        MoneyBag $expectedBalance,
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

    /**
     * Dr wallet Cr incomes 100 USD
     * Dr wallet Cr incomes 150 USD
     * Dr expenses Cr wallet 30 USD
     * Dr wallet Cr incomes 25 USD
     */
    private static function createData1(
        Account $incomesAccount,
        Account $expensesAccount,
        Account $walletAccount
    ): array {
        return [
            [$incomesAccount, $expensesAccount, $walletAccount],
            [
                TestFactory::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    amount: Money::of(100, 'USD'),
                ),
                TestFactory::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    amount: Money::of(150, 'USD'),
                ),
                TestFactory::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount),
                    amount: Money::of(30, 'USD'),
                ),
                TestFactory::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    amount: Money::of(25, 'USD'),
                ),
            ],
        ];
    }

    /**
     * Dr wallet Cr incomes 100 RUB
     * Dr wallet Cr incomes 150 USD
     * Dr wallet Cr incomes 30 USD
     * Dr expenses Cr wallet 71 RUB
     * Dr wallet Cr incomes 22 RUB
     * Dr wallet Cr incomes 500 IDR
     * Dr expenses Cr wallet 15 USD
     * Dr expenses Cr wallet 7 USD
     */
    private static function createData2(
        Account $incomesAccount,
        Account $expensesAccount,
        Account $walletAccount
    ): array {
        return [
            [$incomesAccount, $expensesAccount, $walletAccount],
            [
                TestFactory::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    amount: Money::of(100, 'RUB'),
                ),
                TestFactory::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    amount: Money::of(150, 'USD'),
                ),
                TestFactory::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    amount: Money::of(30, 'USD'),
                ),
                TestFactory::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount),
                    amount: Money::of(71, 'RUB'),
                ),
                TestFactory::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    amount: Money::of(22, 'RUB'),
                ),
                TestFactory::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    amount: Money::of(500, 'IDR'),
                ),
                TestFactory::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount),
                    amount: Money::of(15, 'USD'),
                ),
                TestFactory::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount),
                    amount: Money::of(7, 'USD'),
                ),
            ],
        ];
    }

    /**
     * Dr wallet (John, High) Cr incomes 100 USD
     * Dr wallet (John, Normal) Cr incomes 90 USD
     * Dr wallet (John, High) Cr incomes 5 USD
     * Dr expenses Cr wallet (John, High) 30 USD
     * Dr wallet (Mike, High) Cr incomes 150 USD
     * Dr expenses Cr wallet (John, Normal) 10 USD
     */
    private static function createData3(
        Account $incomesAccount,
        Account $expensesAccount,
        Account $walletAccount
    ): array {
        return [
            [$incomesAccount, $expensesAccount, $walletAccount],
            [
                TestFactory::createPosting(
                    new EntryData($walletAccount, ['contractor' => 'John', 'priority' => 'High']),
                    new EntryData($incomesAccount),
                    amount: Money::of(100, 'USD'),
                ),
                TestFactory::createPosting(
                    new EntryData($walletAccount, ['contractor' => 'John', 'priority' => 'Normal']),
                    new EntryData($incomesAccount),
                    amount: Money::of(90, 'USD'),
                ),
                TestFactory::createPosting(
                    new EntryData($walletAccount, ['contractor' => 'John', 'priority' => 'High']),
                    new EntryData($incomesAccount),
                    amount: Money::of(5, 'USD'),
                ),
                TestFactory::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount, ['contractor' => 'John', 'priority' => 'High']),
                    amount: Money::of(30, 'USD'),
                ),
                TestFactory::createPosting(
                    new EntryData($walletAccount, ['contractor' => 'Mike', 'priority' => 'High']),
                    new EntryData($incomesAccount),
                    amount: Money::of(150, 'USD'),
                ),
                TestFactory::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount, ['contractor' => 'John', 'priority' => 'Normal']),
                    amount: Money::of(10, 'USD'),
                ),
            ],
        ];
    }

    /**
     * 01.12.2020 Dr wallet Cr incomes 100 USD
     * 15.12.2020 Dr wallet Cr incomes 90 USD
     * 25.01.2021 Dr wallet Cr incomes 5 USD
     * 19.02.2021 Dr expenses Cr wallet 30 USD
     * 21.02.2021 Dr wallet Cr incomes 150 USD
     * 05.03.2021 Dr expenses Cr wallet 10 USD
     */
    private static function createData4(
        Account $incomesAccount,
        Account $expensesAccount,
        Account $walletAccount
    ): array {
        return [
            [$incomesAccount, $expensesAccount, $walletAccount],
            [
                TestFactory::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    date: new DateTimeImmutable('01.12.2020'),
                    amount: Money::of(100, 'USD'),
                ),
                TestFactory::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    date: new DateTimeImmutable('15.12.2020'),
                    amount: Money::of(90, 'USD'),
                ),
                TestFactory::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    date: new DateTimeImmutable('25.01.2021'),
                    amount: Money::of(5, 'USD'),
                ),
                TestFactory::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount),
                    date: new DateTimeImmutable('19.02.2021'),
                    amount: Money::of(30, 'USD'),
                ),
                TestFactory::createPosting(
                    new EntryData($walletAccount),
                    new EntryData($incomesAccount),
                    date: new DateTimeImmutable('21.02.2021'),
                    amount: Money::of(150, 'USD'),
                ),
                TestFactory::createPosting(
                    new EntryData($expensesAccount),
                    new EntryData($walletAccount),
                    date: new DateTimeImmutable('05.03.2021'),
                    amount: Money::of(10, 'USD'),
                ),
            ],
        ];
    }
}
