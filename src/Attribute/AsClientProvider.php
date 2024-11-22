<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Attribute;

use Cxxi\ClientProviderBundle\Contracts\ProviderAttributeInterface;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsClientProvider extends AbstractProviderAttribute implements ProviderAttributeInterface
{
    public const TAG = 'client';
    public const STANDALONE = '__STANDALONE__';

    public function __construct(
        private readonly string $name,
        private readonly bool $standalone = false
    ){
        parent::__construct($name);
    }

    public function isStandalone(): bool
    {
        return $this->standalone;
    }
}
