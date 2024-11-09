<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Registry\Model;

use Cxxi\ClientProviderBundle\Contracts\ProviderInterface;
use Cxxi\ClientProviderBundle\Attribute\AsClientProvider;
use Cxxi\ClientProviderBundle\Attribute\AsProvider;
use Cxxi\ClientProviderBundle\Util\AttributeReader;
use Symfony\Component\VarExporter\LazyObjectInterface;

class ClientProviderModel
{
	private readonly string $name;
	private readonly string $providerType;
	private readonly ProviderInterface $instance;

	public function __construct(ProviderInterface $client)
	{
		$reflector = new \ReflectionClass($client);

		if ($reflector->implementsInterface(LazyObjectInterface::class)) {
			$reflector = $reflector->getParentClass();
		}

		$this->name = AttributeReader::get($reflector, AsClientProvider::class, 'name');
		$this->providerType = AttributeReader::get($reflector->getParentClass(), AsProvider::class, 'name');
		$this->instance = $client;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function isProviderType(string $providerType): bool
	{
		return $providerType === $this->providerType;
	}

	public function getProviderType(): string
	{
		return $this->providerType;
	}

	public function getInstance(): ProviderInterface
	{
		return $this->instance;
	}

	public function getFullQualifiedClassName(): string
	{
		return get_class($this->instance);
	}
}