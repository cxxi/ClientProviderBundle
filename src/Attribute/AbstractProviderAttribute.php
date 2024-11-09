<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Attribute;

abstract class AbstractProviderAttribute
{
	public function __construct(
        private readonly string $name
    ){}

    public function getName(): string
    {
        return $this->name;
    }

    public function getTagAlias(): string
    {
        return sprintf('%s.%s', static::TAG, $this->name);
    }

    public function getServiceId(): string
    {
        return sprintf('cxxi.client_provider.%s', $this->getTagAlias());
    }
}