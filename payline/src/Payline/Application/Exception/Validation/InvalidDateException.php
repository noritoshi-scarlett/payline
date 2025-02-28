<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Application\Exception\Validation;

class InvalidDateException extends PaylineValidationException
{
    public function IsOlderDate(\DateTimeImmutable $current, \DateTimeImmutable $last): self
    {
        $this->expandMessageAndSaveParameters(
            sprintf(
                'Given date: %s, last date: %s.',
                $current->format('Y-m-d H:i:s.u'),
                $last->format('Y-m-d H:i:s.u')
            ),
            func_get_args()
        );
        return $this;
    }
}
