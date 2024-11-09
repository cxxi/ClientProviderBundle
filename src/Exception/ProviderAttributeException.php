<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Exception;

class ProviderAttributeException extends \LogicException
{
    public function __construct(string $attribute, string $className)
    {
        parent::__construct(sprintf('Attribute "%s" not found on class "%s".', $attribute, $className));
    }
}