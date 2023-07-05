<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Tests\Calculator;

use Brick\Money\Money;
use Brick\Money\MoneyBag;
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
        $data['base'] = [
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
        $data['base'] = [
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
