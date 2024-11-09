<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Registry\Model;

use Cxxi\ClientProviderBundle\Contracts\ProviderInterface;
use Cxxi\ClientProviderBundle\Util\AttributeReader;
use Cxxi\ClientProviderBundle\Attribute\AsProvider;

class ProviderTypeModel
{
	private readonly string $name;
	private readonly ?string $defaultClient;
	private readonly ?string $fallbackClient;

	public function __construct(\ReflectionClass $reflector)
	{
		$asProvider = AttributeReader::get($reflector, AsProvider::class);

		$this->name = $asProvider->getName();
		$this->defaultClient = $asProvider->getDefault();
		$this->fallbackClient = $asProvider->getFallback();
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getDefaultClient(bool $instance = false): string
	{
		return $instance
			? $this->getInstance($this->defaultClient)
			: $this->defaultClient
		;
	}

	public function getFallbackClient(bool $instance = false): string
	{
		return $instance
			? $this->getInstance($this->fallbackClient)
			: $this->fallbackClient
		;
	}

	private function getInstance(string $className): ProviderInterface
	{
		return $className; // TODO
	}
}