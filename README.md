# PHP Finance - Double Entry

[![Latest Stable Version](https://poser.pugx.org/php-finance/double-entry/v/stable.png)](https://packagist.org/packages/php-finance/double-entry)
[![Total Downloads](https://poser.pugx.org/php-finance/double-entry/downloads.png)](https://packagist.org/packages/php-finance/double-entry)
[![Build status](https://github.com/php-finance/double-entry/workflows/build/badge.svg)](https://github.com/php-finance/double-entry/actions?query=workflow%3Abuild)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fphp-finance%2Fdouble-entry%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/php-finance/double-entry/master)
[![type-coverage](https://shepherd.dev/github/php-finance/double-entry/coverage.svg)](https://shepherd.dev/github/php-finance/double-entry)
[![static analysis](https://github.com/php-finance/double-entry/workflows/static%20analysis/badge.svg)](https://github.com/php-finance/double-entry/actions?query=workflow%3A%22static+analysis%22)
[![psalm-level](https://shepherd.dev/github/php-finance/double-entry/level.svg)](https://shepherd.dev/github/php-finance/double-entry)

[Double entry](https://en.wikipedia.org/wiki/Double-entry_bookkeeping) is an awesome proven approach to financial and analytical accounting. It allows doing accounting of any complexity.

The library features are:

- Generic double entry accounting implementation.
- Multiple data storage backends available as separate packages.
- Transactional.
- Supports sub-accounts and dimensions.
- Avoids precision problems.
- Documentation and examples.

## Glossary

A short glossary is necessary for understanding what's going on.

- **Asset** is something of value the company owns.
- **Liability** is something that company owes to another company, bank or person.
- **Debit** is an accounting entry that results in either increase of assets or decrease in liabilities.
- **Credit** is an accounting entry that results in either decrease of assets or increase in liabilities.
- **Entry** is a debit or a credit entry for a certain account.
- **Posting** is a balanced pair of debit and credit entries that reflects moving of assets or liabilities between accounts.
- **Transaction** is a financial operation that includes one or several postings.
- **Account** is group of entries connected to a certain asset or liability.
- **Subaccount** is used to further specify the purpose of the entries within the account.
- **Dimension** is used to tag entries regardless of the account.
- **Total debit** is a sum of all debit of the account.
- **Total credit** is a sum of all credit of the account.
- **Balance** is an account remainder.
- **Chart of accounts (COA)** is a system of accounts, subaccounts, and dimensions, that allows accounting with the ability to calculate metrics needed.

## Example

Let's assume we want to do accounting for an ice cream company. It has three trucks, each truck has a driver who's the cashier at the same time.
Also they have two storages with one worker in each. Company took out a loan to buy the trucks. Ice-cream is bought from two factories.

The founder wants an accounting to answer the following questions:

1. What's the sales revenue? What's the sales revenue for each truck?
2. What's the net profit margin?
3. What's the equity of the company?
4. What are expenses? How much was spent on salaries, buying ice-cream, trucks and gasoline, storages? How much was paid to each worker / storage / truck / factory?

In terms of accounting that means the following answers:

- Q: How much funds does the company have?
- A: Balance of subaccounts of "Company funds"

- Q: How much we spend?
- A: Total credit of subaccounts of "Company funds"

- Q: How much is spent on buying ice cream?
- A: Total debit of account "Purchases"

- Q: How much is paid to each factory?
- A: Total debit of account "Purchases" grouped by dimension "Counterparty"

- Q: How much is spent on salaries?
- A: Total debit of account "Expenses → Salary"

- Q: How much is spent on salaries of each employee?
- A: Total debit of account "Expenses → Salary" grouped by dimension "Employee"

- Q: How much is spent on truck fuel?
- A: Total debit of account "Expenses → Fuel"

- Q: How much is spent on truck fuel for each truck?
- A: Total debit of account "Expenses → Fuel" grouped by dimension "Truck"

- A: What are total sales revenue?
- Q: Total credit of account "Revenue"

- Q: What are total sales revenue for each truck?
- A: Total debit of "Company funds → Truck" grouped by dimension "Income type"

And that would be the following chart of accounts:

// TODO: add it as a table


Let's add it:

```php
// TODO: add it
```

