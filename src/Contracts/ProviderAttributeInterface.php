<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Contracts;

interface ProviderAttributeInterface
{
	public function getName(): string;

	public function getTagAlias(): string;

	public function getServiceId(): string;
}