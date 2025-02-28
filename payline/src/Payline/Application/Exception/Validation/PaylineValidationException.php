<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Application\Exception\Validation;

class PaylineValidationException extends \Exception
{
    protected(set) array $parametersFromMessage;

    protected function expandMessageAndSaveParameters(string $message, array $parameters): void
    {
        $this->message = trim(sprintf("%s %s", $this->message, $message));
        $this->parametersFromMessage = $parameters;
    }
}