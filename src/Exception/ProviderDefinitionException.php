<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Exception;

class ProviderDefinitionException extends \LogicException
{
    public function __construct(string $className)
    {
        parent::__construct(sprintf('Provider class "%s" must be abstract class.', $className));
    }
}