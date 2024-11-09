<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Exception;

class ProviderAutowireException extends \InvalidArgumentException
{
    public function __construct(string $parameter, string $providerName)
    {
        $message = implode(PHP_EOL, [
            'Cannot autowire argument "$%s": no default client is defined for the "%s" provider.',
            'Please set a default client in the #[AsProvider] attribute of the provider class or pass an explicit client instance as argument".'
        ]);

        parent::__construct(sprintf($message, $parameter, $providerName));
    }
}