<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Exception;

class ProviderTypeException extends \InvalidArgumentException
{
    public function __construct(string $providerType, array $availableTypes)
    {
    	$formated = implode(', ', array_map(fn($t) => sprintf('"%s"', $t), array_keys($availableTypes)));

        parent::__construct(sprintf('Provider type "%s" does not exists. Availables types are: %s', $providerType, $formated));
    }
}