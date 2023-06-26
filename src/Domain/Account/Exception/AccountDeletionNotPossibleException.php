<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Domain\Account\Exception;

use PhpFinance\DoubleEntry\Domain\Account\Account;
use RuntimeException;

final class AccountDeletionNotPossibleException extends RuntimeException
{
    public function __construct(Account $account)
    {
        $name = $account->getName();
        parent::__construct(
            'Deletion not possible, account' . ($name !== null ? ' "' . $name . '"' : '') . ' has children.',
        );
    }
}
