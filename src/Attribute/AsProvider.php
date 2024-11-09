<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Attribute;

use Cxxi\ClientProviderBundle\Contracts\ProviderAttributeInterface;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsProvider extends AbstractProviderAttribute implements ProviderAttributeInterface
{
    public const TAG = 'provider';

    public function __construct(
        private readonly string $name,
        private readonly ?string $default = null,
        private readonly ?string $fallback = null
    ){
        parent::__construct($name);
    }

    public function getDefault(): ?string
    {
        return $this->default;
    }

    public function getFallback(): ?string
    {
        return $this->fallback;
    }
}
