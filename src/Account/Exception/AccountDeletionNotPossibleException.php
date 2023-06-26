<?php

declare(strict_types=1);

namespace PhpFinance\DoubleEntry\Account\Exception;

use PhpFinance\DoubleEntry\Account\Account;
use RuntimeException;

final class AccountDeletionNotPossibleException extends RuntimeException
{
    public function __construct(Account $account)
    {
        parent::__construct(
            sprintf(
                'Deletion not possible, account "%s" has children.',
                $account->getName()
            )
        );
    }
}
