<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Tests\Mock;

#[\Attribute]
class SampleInvalidAttribute
{
    public function __construct(
        private string $name
    ){}

    public function getName(): string
    {
        return $this->name;
    }

    public function getTagAlias(): string
    {
        return 'sample_alias';
    }

    public function getServiceId(): string
    {
        return 'sample_service';
    }
}