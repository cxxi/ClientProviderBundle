<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Exception;

class ClientProviderTypeException extends \InvalidArgumentException
{
    public function __construct(string $clientName, array $availableclients, ?string $currentType = null)
    {
    	$formated = implode(', ', array_map(fn($t) => sprintf('"%s"', $t), array_keys($availableclients)));

        parent::__construct(!is_null($currentType)
            ? sprintf('Provider client "%s" of type "%s" does not exists. Availables clients are: %s', $clientName, $currentType, $formated)
            : sprintf('Provider client "%s" does not exists. Availables clients are: %s', $clientName, $formated)
        );
    }
}