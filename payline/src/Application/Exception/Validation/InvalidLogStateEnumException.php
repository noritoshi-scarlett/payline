<?php
declare(strict_types=1);

namespace Payline\App\Application\Exception\Validation;

use Payline\App\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;

class InvalidLogStateEnumException extends PaylineValidationException
{
    public function states(StateEnumInterface $current, ?StateEnumInterface $last): self
    {
        $this->expandMessageAndSaveParameters(
            sprintf('Given state: %s, last state: %s', $current->name, $last->name ?? 'NONE'),
            func_get_args()
        );

        return $this;
    }
}
